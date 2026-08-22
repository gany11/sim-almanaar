<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\WhatsAppService;

class ReminderKeuanganCron extends BaseCommand
{
    protected $group       = 'WhatsApp';
    protected $name        = 'wa:reminder-keuangan';
    protected $description = 'Kirim reminder WA ke bendahara dan sekretaris terkait laporan mingguan.';

    public function run(array $params)
    {
        CLI::write('Mengecek kondisi reminder keuangan...', 'cyan');

        $db = \Config\Database::connect();
        $whatsapp = new WhatsAppService();

        $hariIni = date('Y-m-d');
        // date('w') menghasilkan angka 0 (Minggu) s/d 6 (Sabtu). 5 adalah Jumat.
        $hariDalamAngka = date('w'); 

        try {
            // =========================================================================
            // KONDISI 1: JIKA HARI INI JUMAT (5)
            // =========================================================================
            if ($hariDalamAngka == 5) {
                CLI::write('Hari Jumat: Mengecek ketersediaan laporan minggu lalu...', 'yellow');

                $kamisKemarin = date('Y-m-d', strtotime('-1 day'));

                // Cek laporan yang ended_at nya kemarin (Kamis)
                $laporanKemarin = $db->table('laporan_mingguan')
                    ->like('ended_at', $kamisKemarin)
                    ->get()
                    ->getRow();

                if ($laporanKemarin) {
                    $targetUsers = $db->table('akun')
                        ->whereIn('id_peran', [3, 4])
                        ->where('status', 'aktif')
                        ->get()
                        ->getResult();

                    $linkLaporan = base_url('admin/finance/report/weekly/' . $laporanKemarin->id_laporan_mingguan);
                    $pesan = "Assalamualaikum Warahmatullahi Wabarakatuh,\n\n"
                                . "Yth. Bendahara dan Sekretaris,\n\n"
                                . "Bersama pesan ini, diinformasikan bahwa Laporan Mingguan Keuangan periode kemarin (*{$laporanKemarin->judul}*) telah siap untuk dicetak.\n\n"
                                . "Silakan akses dan cetak laporan tersebut melalui tautan berikut:\n"
                                . "{$linkLaporan}\n\n"
                                . "Terima kasih atas perhatian dan kerja sama yang diberikan.\n"
                                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                                . "_Disclaimer: Pesan ini dikirim secara otomatis oleh sistem. Mohon abaikan pesan ini apabila laporan tersebut telah diakses atau dicetak._";

                    $bulkJobs = [];

                    foreach ($targetUsers as $user) {

                        if (empty($user->telepon)) {
                            continue;
                        }

                        $bulkJobs[] = [
                            // 'phone' => $user->telepon,
                            'phone'        => '0882 1084 1268',
                            'message'      => $pesan,
                            'message_type' => 'text',
                        ];
                    }

                    if (!empty($bulkJobs)) {

                        $result = $whatsapp->bulkInsert($bulkJobs);

                        CLI::write(
                            'Pesan berhasil dimasukkan ke queue.',
                            'green'
                        );
                    }
                } else {
                    CLI::write('Laporan minggu kemarin tidak ditemukan.', 'red');
                }

            } 
            // =========================================================================
            // KONDISI 2: JIKA HARI INI SABTU (6) s/d KAMIS (4)
            // =========================================================================
            else {
                CLI::write('Hari Sabtu-Kamis: Mengecek input keuangan MINGGU INI...', 'yellow');

                // Dapatkan tanggal hari Jumat terakhir sebagai awal minggu
                $jumatTerakhir = date('Y-m-d', strtotime('last Friday'));

                // Cek apakah laporan mingguan untuk periode minggu ini sudah ada
                // Kita cek berdasarkan started_at yang dimulai pada hari Jumat terakhir
                $laporanMingguIni = $db->table('laporan_mingguan')
                    ->where('started_at >=', $jumatTerakhir . ' 00:00:00')
                    ->get()
                    ->getRow();

                // Cek apakah ada data keuangan created_at SEJAK Jumat terakhir s/d hari ini
                $keuanganMingguIni = $db->table('keuangan')
                    ->whereIn('id_kategori_keuangan', [1, 2, 3, 4])
                    ->where('created_at >=', $jumatTerakhir . ' 00:00:00')
                    ->get()
                    ->getRow();

                // Logika: Jika laporan mingguan KOSONG atau data keuangan minggu ini KOSONG
                if (!$laporanMingguIni || !$keuanganMingguIni) {
                    
                    $bendaharaUsers = $db->table('akun')
                        ->where('id_peran', 4)
                        ->where('status', 'aktif')
                        ->get()
                        ->getResult();

                    $pesan = "Assalamualaikum Warahmatullahi Wabarakatuh,\n\n"
                                . "Yth. Bendahara,\n\n"
                                . "Melalui pesan ini, sistem menyampaikan pengingat terkait data keuangan untuk minggu berjalan (terhitung sejak hari Jumat). Berdasarkan catatan sistem:\n"
                                . "• Belum terdapat input transaksi/data keuangan terbaru, atau\n"
                                . "• Laporan mingguan keuangan belum diterbitkan.\n\n"
                                . "Mohon kesediaannya untuk segera melakukan pengecekan dan pembaruan data yang diperlukan melalui sistem.\n\n"
                                . "Terima kasih atas perhatian dan kerja sama yang diberikan.\n"
                                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                                . "_Disclaimer: Pesan ini dikirim secara otomatis oleh sistem. Mohon abaikan pesan ini apabila pembaruan data atau laporan telah diselesaikan._";

                    $bulkJobs = [];
                    $totalBendahara = 0;

                    foreach ($bendaharaUsers as $bendahara) {

                        if (empty($bendahara->telepon)) {
                            continue;
                        }

                        $bulkJobs[] = [
                            // 'phone'        => $bendahara->telepon,
                            'phone'        => '0882 1084 1268',
                            'message'      => $pesan,
                            'message_type' => 'text',
                        ];

                        $totalBendahara++;
                    }

                    if ($totalBendahara > 0) {

                        $result = $whatsapp->bulkInsert($bulkJobs);

                        CLI::write(
                            "Bulk reminder berhasil dimasukkan ke queue: {$totalBendahara} Bendahara.",
                            'green'
                        );

                    } else {

                        CLI::write(
                            'Tidak ada Bendahara yang memiliki nomor telepon.',
                            'yellow'
                        );
                    }
                } else {
                    CLI::write('Data keuangan minggu ini sudah ada, tidak perlu reminder.', 'green');
                }
            }
            
            log_message('info', 'Cron Reminder Keuangan berhasil dieksekusi.');

        } catch (\Exception $e) {
            CLI::error('Terjadi Kesalahan: ' . $e->getMessage());
            log_message('error', 'Cron Reminder Keuangan Error: ' . $e->getMessage());
        }
    }
}