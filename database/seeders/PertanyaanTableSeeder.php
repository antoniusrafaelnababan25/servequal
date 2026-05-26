<?php

namespace Database\Seeders;

use App\Models\Pertanyaan;
use Illuminate\Database\Seeder;

class PertanyaanTableSeeder extends Seeder
{
    public function run(): void
    {
        // ================================================================
        // 1. PERTANYAAN UNTUK PENILAIAN DOSEN (TARGET: MAHASISWA)
        // Mahasiswa menilai dosen
        // ================================================================

        $pertanyaanPenilaianDosenForMahasiswa = [
            // Tangible (Fisik) - Penampilan dosen
            ['dimensi' => 'Tangible', 'teks' => 'Dosen berpakaian rapi dan profesional saat mengajar'],
            ['dimensi' => 'Tangible', 'teks' => 'Dosen menggunakan media pembelajaran yang menarik (LCD, presentasi, dll)'],
            ['dimensi' => 'Tangible', 'teks' => 'Materi pembelajaran yang disajikan dosen mudah dipahami'],
            ['dimensi' => 'Tangible', 'teks' => 'Dosen menyediakan bahan ajar yang lengkap (modul, slide, dll)'],

            // Reliability (Keandalan) - Konsistensi dosen
            ['dimensi' => 'Reliability', 'teks' => 'Dosen hadir tepat waktu sesuai jadwal yang ditentukan'],
            ['dimensi' => 'Reliability', 'teks' => 'Materi perkuliahan disampaikan sesuai dengan RPS (Rencana Pembelajaran Semester)'],
            ['dimensi' => 'Reliability', 'teks' => 'Dosen memberikan penilaian yang konsisten dan sesuai dengan bobot tugas'],
            ['dimensi' => 'Reliability', 'teks' => 'Dosen mengembalikan hasil tugas/ujian tepat waktu'],
            ['dimensi' => 'Reliability', 'teks' => 'Dosen melaksanakan ujian sesuai jadwal yang ditetapkan'],

            // Responsiveness (Daya Tanggap) - Respon dosen
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen cepat merespon pertanyaan mahasiswa di dalam kelas'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen memberikan umpan balik yang konstruktif terhadap tugas mahasiswa'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen bersedia memberikan bimbingan di luar jam perkuliahan'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen tanggap terhadap kesulitan belajar mahasiswa'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen memberikan informasi yang jelas tentang jadwal dan tugas'],

            // Assurance (Jaminan) - Kompetensi dosen
            ['dimensi' => 'Assurance', 'teks' => 'Dosen memiliki penguasaan materi yang baik dan kompeten di bidangnya'],
            ['dimensi' => 'Assurance', 'teks' => 'Dosen menjelaskan materi dengan cara yang mudah dimengerti'],
            ['dimensi' => 'Assurance', 'teks' => 'Penilaian yang diberikan dosen transparan dan adil'],
            ['dimensi' => 'Assurance', 'teks' => 'Dosen memberikan contoh-contoh relevan untuk memperjelas materi'],
            ['dimensi' => 'Assurance', 'teks' => 'Dosen mampu menjawab pertanyaan mahasiswa dengan baik'],
            ['dimensi' => 'Assurance', 'teks' => 'Dosen memiliki kredibilitas dan wawasan yang luas'],

            // Empathy (Empati) - Perhatian dosen
            ['dimensi' => 'Empathy', 'teks' => 'Dosen memahami kesulitan belajar mahasiswa'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen memberikan perhatian individual kepada mahasiswa yang membutuhkan'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen bersikap ramah dan menghargai mahasiswa'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen mendengarkan aspirasi dan keluhan mahasiswa'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen memberikan motivasi dan semangat belajar kepada mahasiswa'],
        ];

        foreach ($pertanyaanPenilaianDosenForMahasiswa as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => Pertanyaan::TARGET_MAHASISWA,
                    'tipe_penilaian' => Pertanyaan::TYPE_PENILAIAN_DOSEN,
                    'is_active' => true,
                ]
            );
        }

        // ================================================================
        // 2. PERTANYAAN UNTUK PENILAIAN DOSEN (TARGET: BOTH)
        // Mahasiswa DAN Dosen bisa menilai (pertanyaan umum tentang dosen)
        // ================================================================

        $pertanyaanPenilaianDosenForBoth = [
            // Tangible
            ['dimensi' => 'Tangible', 'teks' => 'Dosen memiliki penampilan yang profesional dan rapi'],
            ['dimensi' => 'Tangible', 'teks' => 'Dosen menggunakan teknologi pembelajaran yang up-to-date'],

            // Reliability
            ['dimensi' => 'Reliability', 'teks' => 'Dosen menjalankan tugas pengajaran sesuai dengan standar yang ditetapkan'],
            ['dimensi' => 'Reliability', 'teks' => 'Dosen memberikan pelayanan akademik yang konsisten'],

            // Responsiveness
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen tanggap terhadap kebutuhan dan keluhan stakeholders'],
            ['dimensi' => 'Responsiveness', 'teks' => 'Dosen cepat beradaptasi dengan perubahan kebijakan akademik'],

            // Assurance
            ['dimensi' => 'Assurance', 'teks' => 'Dosen memiliki kompetensi yang diakui di bidangnya'],
            ['dimensi' => 'Assurance', 'teks' => 'Dosen melaksanakan penilaian secara profesional dan objektif'],

            // Empathy
            ['dimensi' => 'Empathy', 'teks' => 'Dosen menunjukkan kepedulian terhadap perkembangan mahasiswa'],
            ['dimensi' => 'Empathy', 'teks' => 'Dosen membangun komunikasi yang baik dengan civitas akademika'],
        ];

        foreach ($pertanyaanPenilaianDosenForBoth as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => Pertanyaan::TARGET_BOTH,
                    'tipe_penilaian' => Pertanyaan::TYPE_PENILAIAN_DOSEN,
                    'is_active' => true,
                ]
            );
        }

        // ================================================================
        // 3. PERTANYAAN UNTUK PENILAIAN FASILITAS (TARGET: DOSEN)
        // Dosen menilai fasilitas kampus
        // ================================================================

        $pertanyaanPenilaianFasilitasForDosen = [
            // Kategori: Umum
            ['kategori' => 'umum', 'dimensi' => 'Tangible', 'teks' => 'Fasilitas ruang dosen (meja, kursi, lemari) dalam kondisi baik'],
            ['kategori' => 'umum', 'dimensi' => 'Tangible', 'teks' => 'Kebersihan lingkungan kampus terjaga dengan baik'],
            ['kategori' => 'umum', 'dimensi' => 'Tangible', 'teks' => 'Tata ruang dan pencahayaan di area kerja dosen nyaman'],
            ['kategori' => 'umum', 'dimensi' => 'Reliability', 'teks' => 'Fasilitas umum kampus berfungsi dengan andal'],
            ['kategori' => 'umum', 'dimensi' => 'Responsiveness', 'teks' => 'Keluhan tentang fasilitas umum cepat ditindaklanjuti'],
            ['kategori' => 'umum', 'dimensi' => 'Assurance', 'teks' => 'Fasilitas umum kampus aman untuk digunakan'],
            ['kategori' => 'umum', 'dimensi' => 'Empathy', 'teks' => 'Pihak kampus peduli dengan kenyamanan dosen dalam menggunakan fasilitas'],

            // Kategori: Peralatan
            ['kategori' => 'peralatan', 'dimensi' => 'Tangible', 'teks' => 'Komputer dan laptop untuk dosen dalam kondisi baik'],
            ['kategori' => 'peralatan', 'dimensi' => 'Tangible', 'teks' => 'Proyektor dan sound system di ruang kuliah berfungsi dengan baik'],
            ['kategori' => 'peralatan', 'dimensi' => 'Tangible', 'teks' => 'Peralatan laboratorium dan praktikum lengkap dan berfungsi'],
            ['kategori' => 'peralatan', 'dimensi' => 'Reliability', 'teks' => 'Peralatan pendukung pengajaran selalu tersedia saat dibutuhkan'],
            ['kategori' => 'peralatan', 'dimensi' => 'Responsiveness', 'teks' => 'Perbaikan peralatan yang rusak cepat ditangani'],
            ['kategori' => 'peralatan', 'dimensi' => 'Assurance', 'teks' => 'Kualitas peralatan pengajaran memenuhi standar yang ditetapkan'],
            ['kategori' => 'peralatan', 'dimensi' => 'Empathy', 'teks' => 'Terdapat perhatian terhadap kebutuhan peralatan terbaru untuk dosen'],

            // Kategori: Ruangan
            ['kategori' => 'ruangan', 'dimensi' => 'Tangible', 'teks' => 'Ruangan kelas bersih, nyaman, dan memiliki sirkulasi udara baik'],
            ['kategori' => 'ruangan', 'dimensi' => 'Tangible', 'teks' => 'Ruangan laboratorium memadai untuk kegiatan praktikum'],
            ['kategori' => 'ruangan', 'dimensi' => 'Tangible', 'teks' => 'Ruang sidang dan ruang rapat representatif'],
            ['kategori' => 'ruangan', 'dimensi' => 'Reliability', 'teks' => 'Ketersediaan ruangan sesuai dengan jadwal yang ditentukan'],
            ['kategori' => 'ruangan', 'dimensi' => 'Responsiveness', 'teks' => 'Pemesanan ruangan mudah dan cepat diproses'],
            ['kategori' => 'ruangan', 'dimensi' => 'Assurance', 'teks' => 'Keamanan ruangan terjamin dengan sistem kunci dan CCTV'],
            ['kategori' => 'ruangan', 'dimensi' => 'Empathy', 'teks' => 'Terdapat ruang khusus yang nyaman untuk konsultasi dosen-mahasiswa'],

            // Kategori: Akses
            ['kategori' => 'akses', 'dimensi' => 'Tangible', 'teks' => 'Akses internet Wi-Fi tersedia di seluruh area kampus'],
            ['kategori' => 'akses', 'dimensi' => 'Tangible', 'teks' => 'Sistem informasi akademik (SIAKAD) mudah diakses'],
            ['kategori' => 'akses', 'dimensi' => 'Reliability', 'teks' => 'Koneksi internet stabil dan cepat untuk mendukung pengajaran'],
            ['kategori' => 'akses', 'dimensi' => 'Responsiveness', 'teks' => 'Gangguan akses internet cepat ditangani oleh teknisi'],
            ['kategori' => 'akses', 'dimensi' => 'Assurance', 'teks' => 'Data akademik dosen dan mahasiswa aman tersimpan'],
            ['kategori' => 'akses', 'dimensi' => 'Empathy', 'teks' => 'Terdapat layanan helpdesk yang membantu masalah akses sistem'],

            // Kategori: Infrastruktur
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Area parkir kampus memadai dan tertata rapi'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Toilet kampus bersih dan terawat'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Musholla dan fasilitas ibadah nyaman digunakan'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Reliability', 'teks' => 'Ketersediaan air bersih dan listrik terjamin'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Responsiveness', 'teks' => 'Perbaikan infrastruktur yang rusak cepat ditangani'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Assurance', 'teks' => 'Bangunan kampus memenuhi standar keamanan'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Empathy', 'teks' => 'Terdapat fasilitas yang ramah bagi dosen berkebutuhan khusus'],
        ];

        foreach ($pertanyaanPenilaianFasilitasForDosen as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => Pertanyaan::TARGET_DOSEN,
                    'tipe_penilaian' => Pertanyaan::TYPE_PENILAIAN_FASILITAS,
                    'kategori_fasilitas' => $p['kategori'],
                    'is_active' => true,
                ]
            );
        }

        // ================================================================
        // 4. PERTANYAAN UNTUK PENILAIAN FASILITAS (TARGET: MAHASISWA)
        // Mahasiswa menilai fasilitas kampus
        // ================================================================

        $pertanyaanPenilaianFasilitasForMahasiswa = [
            // Kategori: Umum
            ['kategori' => 'umum', 'dimensi' => 'Tangible', 'teks' => 'Fasilitas ruang kuliah (AC, kursi, meja) dalam kondisi baik dan nyaman'],
            ['kategori' => 'umum', 'dimensi' => 'Tangible', 'teks' => 'Kebersihan dan kenyamanan lingkungan kampus terjaga'],
            ['kategori' => 'umum', 'dimensi' => 'Reliability', 'teks' => 'Fasilitas umum kampus berfungsi dengan baik saat dibutuhkan'],
            ['kategori' => 'umum', 'dimensi' => 'Responsiveness', 'teks' => 'Keluhan mahasiswa tentang fasilitas cepat ditindaklanjuti'],
            ['kategori' => 'umum', 'dimensi' => 'Assurance', 'teks' => 'Keamanan fasilitas kampus terjamin untuk digunakan'],
            ['kategori' => 'umum', 'dimensi' => 'Empathy', 'teks' => 'Pihak kampus peduli dengan kenyamanan mahasiswa dalam menggunakan fasilitas'],

            // Kategori: Peralatan
            ['kategori' => 'peralatan', 'dimensi' => 'Tangible', 'teks' => 'Proyektor dan sound system di ruang kuliah berfungsi dengan baik'],
            ['kategori' => 'peralatan', 'dimensi' => 'Tangible', 'teks' => 'Peralatan laboratorium komputer/lab bahasa berfungsi dengan baik'],
            ['kategori' => 'peralatan', 'dimensi' => 'Reliability', 'teks' => 'Peralatan praktikum selalu tersedia saat jam praktikum'],
            ['kategori' => 'peralatan', 'dimensi' => 'Responsiveness', 'teks' => 'Perbaikan peralatan yang rusak cepat ditangani'],
            ['kategori' => 'peralatan', 'dimensi' => 'Assurance', 'teks' => 'Kualitas peralatan pembelajaran memenuhi standar'],
            ['kategori' => 'peralatan', 'dimensi' => 'Empathy', 'teks' => 'Terdapat peralatan pendukung yang memudahkan proses belajar'],

            // Kategori: Ruangan
            ['kategori' => 'ruangan', 'dimensi' => 'Tangible', 'teks' => 'Ruangan kelas bersih, nyaman, dan memiliki pencahayaan baik'],
            ['kategori' => 'ruangan', 'dimensi' => 'Tangible', 'teks' => 'Laboratorium dan ruang praktikum memadai untuk kegiatan belajar'],
            ['kategori' => 'ruangan', 'dimensi' => 'Reliability', 'teks' => 'Ruangan perpustakaan nyaman untuk belajar'],
            ['kategori' => 'ruangan', 'dimensi' => 'Responsiveness', 'teks' => 'Peminjaman ruangan untuk kegiatan mahasiswa mudah diproses'],
            ['kategori' => 'ruangan', 'dimensi' => 'Assurance', 'teks' => 'Keamanan ruangan kelas dan laboratorium terjamin'],
            ['kategori' => 'ruangan', 'dimensi' => 'Empathy', 'teks' => 'Terdapat ruang diskusi yang nyaman untuk belajar kelompok'],

            // Kategori: Akses
            ['kategori' => 'akses', 'dimensi' => 'Tangible', 'teks' => 'Akses internet Wi-Fi tersedia di seluruh area kampus'],
            ['kategori' => 'akses', 'dimensi' => 'Tangible', 'teks' => 'Portal akademik (SIAKAD/SIMAK) mudah diakses dan user-friendly'],
            ['kategori' => 'akses', 'dimensi' => 'Reliability', 'teks' => 'Koneksi internet stabil untuk mengakses materi pembelajaran online'],
            ['kategori' => 'akses', 'dimensi' => 'Responsiveness', 'teks' => 'Gangguan akses internet cepat ditangani'],
            ['kategori' => 'akses', 'dimensi' => 'Assurance', 'teks' => 'Keamanan data pribadi mahasiswa terjamin'],
            ['kategori' => 'akses', 'dimensi' => 'Empathy', 'teks' => 'Terdapat layanan bantuan (helpdesk) untuk masalah akses sistem'],

            // Kategori: Infrastruktur
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Area parkir kampus memadai dan aman'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Toilet kampus bersih dan tersedia di setiap gedung'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Musholla dan fasilitas ibadah bersih dan nyaman'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Reliability', 'teks' => 'Ketersediaan air bersih dan listrik terjamin selama jam kuliah'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Responsiveness', 'teks' => 'Kerusakan infrastruktur cepat diperbaiki'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Assurance', 'teks' => 'Bangunan kampus terawat dan aman untuk aktivitas belajar'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Empathy', 'teks' => 'Terdapat fasilitas yang ramah bagi mahasiswa berkebutuhan khusus (ramp, toilet khusus)'],
        ];

        foreach ($pertanyaanPenilaianFasilitasForMahasiswa as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => Pertanyaan::TARGET_MAHASISWA,
                    'tipe_penilaian' => Pertanyaan::TYPE_PENILAIAN_FASILITAS,
                    'kategori_fasilitas' => $p['kategori'],
                    'is_active' => true,
                ]
            );
        }

        // ================================================================
        // 5. PERTANYAAN UNTUK PENILAIAN FASILITAS (TARGET: BOTH)
        // Mahasiswa DAN Dosen bisa menilai (pertanyaan umum tentang fasilitas)
        // ================================================================

        $pertanyaanPenilaianFasilitasForBoth = [
            // Kategori: Umum
            ['kategori' => 'umum', 'dimensi' => 'Tangible', 'teks' => 'Fasilitas kampus secara keseluruhan mendukung kegiatan akademik'],
            ['kategori' => 'umum', 'dimensi' => 'Reliability', 'teks' => 'Fasilitas kampus berfungsi sesuai standar yang diharapkan'],
            ['kategori' => 'umum', 'dimensi' => 'Responsiveness', 'teks' => 'Pemeliharaan fasilitas dilakukan secara rutin dan terjadwal'],
            ['kategori' => 'umum', 'dimensi' => 'Assurance', 'teks' => 'Fasilitas kampus memberikan kenyamanan dalam proses pembelajaran'],
            ['kategori' => 'umum', 'dimensi' => 'Empathy', 'teks' => 'Pengembangan fasilitas mempertimbangkan kebutuhan pengguna'],

            // Kategori: Peralatan
            ['kategori' => 'peralatan', 'dimensi' => 'Tangible', 'teks' => 'Peralatan penunjang akademik tersedia dengan kualitas baik'],
            ['kategori' => 'peralatan', 'dimensi' => 'Reliability', 'teks' => 'Peralatan pembelajaran selalu siap digunakan saat diperlukan'],
            ['kategori' => 'peralatan', 'dimensi' => 'Responsiveness', 'teks' => 'Pengadaan peralatan baru sesuai dengan perkembangan teknologi'],

            // Kategori: Ruangan
            ['kategori' => 'ruangan', 'dimensi' => 'Tangible', 'teks' => 'Ruangan perkuliahan memenuhi standar kenyamanan'],
            ['kategori' => 'ruangan', 'dimensi' => 'Reliability', 'teks' => 'Penjadwalan ruangan dilakukan dengan baik dan tidak bentrok'],

            // Kategori: Akses
            ['kategori' => 'akses', 'dimensi' => 'Tangible', 'teks' => 'Sistem informasi akademik mudah diakses oleh seluruh civitas'],
            ['kategori' => 'akses', 'dimensi' => 'Reliability', 'teks' => 'Layanan digital kampus tersedia 24 jam dengan baik'],

            // Kategori: Infrastruktur
            ['kategori' => 'infrastruktur', 'dimensi' => 'Tangible', 'teks' => 'Bangunan dan infrastruktur kampus terawat dengan baik'],
            ['kategori' => 'infrastruktur', 'dimensi' => 'Reliability', 'teks' => 'Sistem utilitas (listrik, air, jaringan) berfungsi andal'],
        ];

        foreach ($pertanyaanPenilaianFasilitasForBoth as $p) {
            Pertanyaan::updateOrCreate(
                ['teks' => $p['teks']],
                [
                    'dimensi' => $p['dimensi'],
                    'target_role' => Pertanyaan::TARGET_BOTH,
                    'tipe_penilaian' => Pertanyaan::TYPE_PENILAIAN_FASILITAS,
                    'kategori_fasilitas' => $p['kategori'],
                    'is_active' => true,
                ]
            );
        }

        // Output ke console
        $this->command->info('PertanyaanTableSeeder: Berhasil menambahkan data pertanyaan!');

        $total = Pertanyaan::count();
        $this->command->info("Total pertanyaan dalam database: {$total}");

        $this->command->info("\n📊 Statistik berdasarkan target role:");
        $this->command->info("- Mahasiswa: " . Pertanyaan::where('target_role', Pertanyaan::TARGET_MAHASISWA)->count() . " pertanyaan");
        $this->command->info("- Dosen: " . Pertanyaan::where('target_role', Pertanyaan::TARGET_DOSEN)->count() . " pertanyaan");
        $this->command->info("- Both: " . Pertanyaan::where('target_role', Pertanyaan::TARGET_BOTH)->count() . " pertanyaan");

        $this->command->info("\n📊 Statistik berdasarkan tipe penilaian:");
        $this->command->info("- Penilaian Dosen: " . Pertanyaan::where('tipe_penilaian', Pertanyaan::TYPE_PENILAIAN_DOSEN)->count() . " pertanyaan");
        $this->command->info("- Penilaian Fasilitas: " . Pertanyaan::where('tipe_penilaian', Pertanyaan::TYPE_PENILAIAN_FASILITAS)->count() . " pertanyaan");
    }
}