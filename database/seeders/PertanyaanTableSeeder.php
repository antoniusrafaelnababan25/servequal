<?php

namespace Database\Seeders;

use App\Models\Pertanyaan;
use Illuminate\Database\Seeder;

class PertanyaanTableSeeder extends Seeder
{
    public function run(): void
    {
        // Pertanyaan untuk mahasiswa
        $pertanyaanMahasiswa = [
            ['dimensi' => 'Tangible', 'teks' => 'Fasilitas ruang kuliah (AC, LCD, kursi) dalam kondisi baik dan nyaman'],
            ['dimensi' => 'Tangible', 'teks' => 'Kebersihan dan kenyamanan lingkungan kampus'],
            ['dimensi' => 'Reliability', 'teks' => 'Dosen hadir tepat waktu sesuai jadwal yang ditentukan'],
            ['dimensi' => 'Reliability', 'teks' => 'Materi perkuliahan disampaikan sesuai dengan RPS'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen cepat merespon pertanyaan mahasiswa di kelas'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Staf akademik tanggap dalam melayani administrasi mahasiswa'],
            ['dimensi' => 'Assurance', 'teks' => 'Dosen memiliki penguasaan materi yang baik dan kompeten'],
            ['dimensi' => 'Assurance', 'teks' => 'Penilaian yang diberikan dosen transparan dan adil'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen memahami kesulitan belajar mahasiswa'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen memberikan perhatian individual kepada mahasiswa'],
        ];

        foreach ($pertanyaanMahasiswa as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => 'mahasiswa',
                    'is_active' => true,
                ]
            );
        }

        // Pertanyaan untuk dosen (fasilitas mengajar)
        $pertanyaanDosen = [
            ['dimensi' => 'Tangible', 'teks' => 'Fasilitas laboratorium dan ruang kerja dosen memadai'],
            ['dimensi' => 'Tangible', 'teks' => 'Ketersediaan sarana mengajar (proyektor, papan tulis, dll)'],
            ['dimensi' => 'Reliability', 'teks' => 'Mahasiswa mengumpulkan tugas tepat waktu'],
            ['dimensi' => 'Reliability', 'teks' => 'Kehadiran mahasiswa dalam perkuliahan konsisten'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Admin akademik responsif terhadap keluhan dosen'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Proses pengajuan perangkat perkuliahan cepat diproses'],
            ['dimensi' => 'Assurance', 'teks' => 'Keamanan data nilai dan administrasi terjamin'],
            ['dimensi' => 'Assurance', 'teks' => 'Kebijakan akademik jelas dan konsisten'],
            ['dimensi' => 'Empathy', 'teks' => 'Pimpinan mendengar dan menindaklanjuti aspirasi dosen'],
            ['dimensi' => 'Empathy', 'teks' => 'Terdapat penghargaan untuk kinerja dosen yang baik'],
        ];

        foreach ($pertanyaanDosen as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => 'dosen',
                    'is_active' => true,
                ]
            );
        }
    }
}