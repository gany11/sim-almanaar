<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\WhatsAppService;

class SendWhatsAppCron extends BaseCommand
{
    // Grup untuk mengkategorikan command di spark
    protected $group       = 'WhatsApp';
    
    // Nama perintah yang akan dieksekusi
    protected $name        = 'wa:send-image';
    
    // Deskripsi singkat
    protected $description = 'Mengirim pesan gambar WhatsApp secara otomatis.';

    public function run(array $params)
    {
        CLI::write('Memulai proses pengiriman WhatsApp...', 'yellow');

        try {
            $whatsapp = new WhatsAppService();

            $result = $whatsapp->send(
                phone: '0882 1084 1268',
                message: "*Halo semuanya!*\n\nIni adalah pesan _percobaan_ dari WhatsApp Gateway.\n\n*Daftar kegiatan:*\n- Sholat berjamaah\n- Kajian rutin\n- Santunan\n\n*Catatan penting:*\n> Harap hadir tepat waktu.\n\nTerima kasih.",
                messageType: 'image',
                mediaType: 'image',
                mediaUrl: "https://almanaar-slipi.org/uploads/berita/1782626762_e6dae7dbe6a48bd8db5d.jpeg",
                filename: "1782626762_e6dae7dbe6a48bd8db5d.jpeg"
            );

            // Tampilkan pesan sukses di terminal
            CLI::write('Sukses: Gambar berhasil dikirim.', 'green');
            
            // Catat ke dalam log CI4 (writable/logs) untuk riwayat cronjob
            log_message('info', 'Cron WhatsApp Berhasil: ' . json_encode($result));

        } catch (\Throwable $e) {
            
            // Tampilkan pesan error di terminal
            CLI::error('Gagal: ' . $e->getMessage());
            
            // Catat error ke dalam log CI4
            log_message('error', 'Cron WhatsApp Error: ' . $e->getMessage());
        }
    }
}