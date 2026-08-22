<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\WhatsAppService;

class ReminderAgendaCron extends BaseCommand
{
    protected $group       = 'WhatsApp';
    protected $name        = 'wa:reminder-agenda';
    protected $description = 'Kirim reminder agenda harian ke grup WhatsApp.';

    public function run(array $params)
    {
        CLI::write('Mengecek agenda hari ini...', 'cyan');

        helper('tanggal'); 

        $db = \Config\Database::connect();
        
        $hariIni = date('Y-m-d');

        try {
            // 1. Ambil data agenda HARI INI
            $builder = $db->table('agenda')
                ->select('agenda.*, kategori_agenda.nama_kategori, kw_mulai.keterangan as ket_mulai, kw_selesai.keterangan as ket_selesai')
                ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda')
                ->join('keterangan_waktu as kw_mulai', 'kw_mulai.id_keterangan_waktu = agenda.id_keterangan_waktu_mulai', 'left')
                ->join('keterangan_waktu as kw_selesai', 'kw_selesai.id_keterangan_waktu = agenda.id_keterangan_waktu_selesai', 'left')
                ->like('waktu_mulai', $hariIni)
                ->where('agenda.deleted_at', null);

            $agendas = $builder->get()->getResultArray();

            // 2. Jika tidak ada agenda, hentikan eksekusi (tidak kirim WA)
            if (empty($agendas)) {
                CLI::write('Tidak ada agenda hari ini. Menghentikan proses.', 'yellow');
                return;
            }

            $tanggalMasehi = format_indo($hariIni, 'full');
            $tanggalHijriah = format_hijriah($hariIni);

            // 3. Rangkai Pesan WhatsApp
            $pesan = "Assalamualaikum Warahmatullahi Wabarakatuh,\n\n";
            $pesan .= "Yth. Jamaah Masjid Al Manaar Slipi,\n\n";
            $pesan .= "Melalui pesan ini, sistem menyampaikan informasi dan pengingat terkait agenda kegiatan di Masjid Al Manaar pada hari ini (*{$tanggalMasehi} | {$tanggalHijriah}*). Berikut adalah rinciannya:\n\n";

            foreach ($agendas as $index => $row) {
                $no = $index + 1;
                // --- LOGIKA JUDUL & TEMA ---
                $teksHeader = "";
                
                if (!empty($row['tema']) && !empty($row['judul'])) {
                    $teksHeader = $row['tema'] . " - " . $row['judul'];
                } elseif (!empty($row['tema'])) {
                    $teksHeader = $row['tema'];
                } elseif (!empty($row['judul'])) {
                    $teksHeader = $row['judul'];
                }

                // Masukkan ke dalam pesan
                if ($teksHeader != "") {
                    $pesan .= "*{$no}. [{$row['nama_kategori']}] {$teksHeader}*\n";
                } else {
                    $pesan .= "*{$no}. [{$row['nama_kategori']}]*\n";
                }

                // --- LOGIKA WAKTU ---
                $waktuText = "";
                
                if ($row['id_kategori_agenda'] == 1) {
                    $waktuText = date('H:i', strtotime($row['waktu_mulai'])) . " WIB";
                } else {
                    $waktuMulaiStr = "";
                    $isMulaiJam = false;

                    // Cek keterangan waktu mulai
                    if (!empty($row['ket_mulai'])) {
                        $waktuMulaiStr = $row['ket_mulai'];
                        $isMulaiJam = false;
                    } else {
                        $waktuMulaiStr = date('H:i', strtotime($row['waktu_mulai']));
                        $isMulaiJam = true;
                    }

                    $waktuSelesaiStr = "";
                    $isSelesaiJam = false;

                    // Cek keterangan waktu selesai
                    if (!empty($row['ket_selesai'])) {
                        $waktuSelesaiStr = $row['ket_selesai'];
                        $isSelesaiJam = false;
                    } elseif (!empty($row['waktu_selesai']) && $row['waktu_selesai'] != $row['waktu_mulai']) {
                        $waktuSelesaiStr = date('H:i', strtotime($row['waktu_selesai']));
                        $isSelesaiJam = true;
                    }

                    // --- RANGKAI STRING WAKTU ---
                    
                    if ($waktuSelesaiStr != "") {
                        $teksMulai = $isMulaiJam ? $waktuMulaiStr . " WIB" : $waktuMulaiStr;
                        $teksSelesai = $isSelesaiJam ? $waktuSelesaiStr . " WIB" : $waktuSelesaiStr;
                        
                        $waktuText = $teksMulai . " - " . $teksSelesai;
                    } 
                    else {
                        $waktuText = $isMulaiJam ? $waktuMulaiStr . " WIB" : $waktuMulaiStr;
                    }
                }
                
                $pesan .= "• Waktu   : {$waktuText}\n";
                
                if (!empty($row['tempat'])) {
                    $pesan .= "• Tempat  : {$row['tempat']}\n";
                }

                // --- LOGIKA SDM / PENGISI ---
                $pengisi = $db->table('sdm_agenda')
                    ->select('sdm.nama, kategori_sdm.kategori as peran')
                    ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                    ->join('kategori_sdm', 'kategori_sdm.id_kategori_sdm = sdm_agenda.id_kategori_sdm')
                    ->where('id_agenda', $row['id_agenda'])
                    ->get()
                    ->getResultArray();

                if (!empty($pengisi)) {
                    foreach ($pengisi as $p) {
                        $pesan .= "• {$p['peran']} : {$p['nama']}\n";
                    }
                }

                if (!empty($row['deskripsi'])) {
                    $deskripsiClean = $row['deskripsi'];

                    // 1. Ubah tag <li> menjadi baris baru dengan awalan strip "- "
                    $deskripsiClean = preg_replace('/<li>/i', "\n- ", $deskripsiClean);
                    
                    // 2. Ubah tag penutup paragraf, list, atau br menjadi baris baru (newline)
                    $deskripsiClean = preg_replace('/<br\s*\/?>/i', "\n", $deskripsiClean);
                    $deskripsiClean = preg_replace('/<\/p>/i', "\n", $deskripsiClean);
                    $deskripsiClean = preg_replace('/<\/(ul|ol)>/i', "\n", $deskripsiClean);

                    // 3. Hapus SEMUA tag HTML yang tersisa (termasuk <span>, <a>, <ul>, <ol>, dll)
                    $deskripsiClean = strip_tags($deskripsiClean);

                    // 4. Ubah entitas HTML (seperti &nbsp;, &amp;) menjadi teks aslinya
                    $deskripsiClean = html_entity_decode($deskripsiClean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    // 5. Bersihkan baris kosong yang berlebihan (imbas dari penghapusan tag)
                    // Mengubah 2 baris kosong atau lebih menjadi maksimal 1 baris kosong saja
                    $deskripsiClean = preg_replace("/\n\s*\n/", "\n", $deskripsiClean);
                    
                    // 6. Bersihkan spasi di awal dan akhir keseluruhan teks
                    $deskripsiClean = trim($deskripsiClean);

                    if (!empty($deskripsiClean)) {
                        // Jika ada deskripsi, taruh di baris baru agar lebih rapi dibaca
                        $pesan .= "• Catatan :\n{$deskripsiClean}\n";
                    }
                }

                $pesan .= "\n";
            }

            // Penutup Pesan
            $pesan .= "Semoga seluruh kegiatan hari ini dapat berjalan lancar dan membawa keberkahan bagi kita semua. Mohon kehadirannya sesuai waktu yang telah ditentukan.\n\n";
            $pesan .= "Terima kasih atas perhatian dan kerja sama yang diberikan.\n";
            $pesan .= "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n";
            $pesan .= "_Disclaimer: Pesan ini dikirim secara otomatis oleh sistem. Mohon abaikan pesan ini apabila terdapat perubahan jadwal yang belum diperbarui._";

            // 4. Daftar Grup
            $targetGroups = [
                '120363048133903233@g.us',
                // '123456789012345678@g.us',
            ];


            // 5. Siapkan Queue Jobs
            $jobs = [];

            foreach ($targetGroups as $groupId) {

                $jobs[] = [
                    'group_id' =>
                        $groupId,

                    'message' =>
                        $pesan,

                    'message_type' =>
                        'text',
                ];

            }


            // 6. Masukkan seluruh pesan ke Queue
            $whatsapp =
                new WhatsAppService();

            $result =
                $whatsapp->bulkInsert(
                    $jobs
                );


            // 7. Log
            CLI::write(
                'Pesan agenda berhasil dimasukkan ke queue untuk ' .
                count($jobs) .
                ' grup.',
                'green'
            );

            log_message('info', 'Cron Agenda Harian berhasil dieksekusi.');

        } catch (\Exception $e) {
            CLI::error('Terjadi Kesalahan: ' . $e->getMessage());
            log_message('error', 'Cron Agenda Harian Error: ' . $e->getMessage());
        }
    }
}