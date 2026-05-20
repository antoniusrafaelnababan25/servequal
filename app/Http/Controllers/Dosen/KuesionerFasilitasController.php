<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use App\Models\KuesionerResponse;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KuesionerFasilitasController extends Controller
{
    /**
     * Tampilkan form kuisioner fasilitas
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $dosen = Auth::user();

        // Cek apakah sudah pernah mengisi
        $sudahMengisi = KuesionerResponse::where('responden_id', $dosen->id)
            ->where('role', 'dosen')
            ->exists();

        if ($sudahMengisi) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Anda sudah mengisi kuesioner fasilitas sebelumnya.');
        }

        // Ambil pertanyaan untuk dosen (kuesioner fasilitas)
        $pertanyaan = Pertanyaan::where('target_role', 'dosen')
            ->where('is_active', true)
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();

        // Kelompokkan pertanyaan berdasarkan dimensi
        $pertanyaanByDimensi = [];
        foreach ($pertanyaan as $q) {
            $dimensi = $q->dimensi;
            if (!isset($pertanyaanByDimensi[$dimensi])) {
                $pertanyaanByDimensi[$dimensi] = [];
            }
            $pertanyaanByDimensi[$dimensi][] = $q;
        }

        return view('dosen.kuesioner-fasilitas.create', compact('pertanyaan', 'pertanyaanByDimensi'));
    }

    /**
     * Simpan jawaban kuisioner fasilitas
     *
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $dosen = Auth::user();

        // Cek duplikat
        if (KuesionerResponse::where('responden_id', $dosen->id)->where('role', 'dosen')->exists()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda sudah pernah mengisi.'], 400);
            }
            return redirect()->back()->with('error', 'Anda sudah pernah mengisi.');
        }

        // Validasi
        $rules = [
            'jawaban' => 'required|array',
            'jawaban.*.id_pertanyaan' => 'required|exists:pertanyaan,id',
            'jawaban.*.persepsi' => 'required|integer|min:1|max:5',
            'jawaban.*.harapan' => 'required|integer|min:1|max:5',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Siapkan data jawaban
        $jawabanData = [];
        foreach ($request->jawaban as $item) {
            $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
            $jawabanData[] = [
                'id_pertanyaan' => $item['id_pertanyaan'],
                'pertanyaan' => $pertanyaan ? $pertanyaan->teks : '',
                'dimensi' => $pertanyaan ? $pertanyaan->dimensi : '',
                'persepsi' => (int) $item['persepsi'],
                'harapan' => (int) $item['harapan'],
            ];
        }

        // Hitung rata-rata persepsi keseluruhan
        $rataRata = collect($jawabanData)->avg('persepsi');

        // Simpan ke database
        $response = KuesionerResponse::create([
            'responden_id' => $dosen->id,
            'responden_nama' => $dosen->name,
            'responden_nidn' => $dosen->nidn,
            'role' => 'dosen',
            'kelas' => null,
            'mata_kuliah' => null,
            'dosen_id' => null,
            'dosen_nama' => null,
            'jawaban' => $jawabanData,
            'rata_rata' => round($rataRata, 2),
        ]);

        // Kirim notifikasi ke admin (opsional)
        try {
            Notifikasi::create([
                'type' => 'kuesioner_fasilitas',
                'title' => 'Kuesioner Fasilitas Diisi',
                'message' => "Dosen {$dosen->name} telah mengisi kuesioner fasilitas.",
                'target_role' => 'admin',
                'target_user_id' => null,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            // Abaikan jika notifikasi gagal
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Terima kasih telah mengisi kuesioner.']);
        }

        return redirect()->route('dosen.kuesioner-fasilitas.thankyou');
    }

    /**
     * Halaman terima kasih setelah mengisi
     *
     * @return \Illuminate\View\View
     */
    public function thankyou()
    {
        return view('dosen.kuesioner-fasilitas.thankyou');
    }
}