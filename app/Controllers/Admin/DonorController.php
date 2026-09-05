<?php

namespace App\Controllers\Admin;

use App\Models\DonaturModel;
use App\Models\PemasukanDonasiModel;
use App\Models\HistoriStatusDonasiModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Services\WhatsAppService;

class DonorController extends BaseController
{
    protected $donaturModel;
    protected $pemasukanDonasiModel;
    protected WhatsAppService $whatsappService;
    protected $historiStatusDonasiModel;

    public function __construct() {
        $this->donaturModel = new DonaturModel();
        $this->pemasukanDonasiModel = new PemasukanDonasiModel();
        $this->whatsappService = new WhatsAppService();
        $this->historiStatusDonasiModel = new HistoriStatusDonasiModel();
    }

    public function index()
    {
        return view('admin/donor/v_index', ['title' => 'Manajemen Donatur']);
    }

    public function list()
    {
        $data['donors'] = $this->donaturModel->orderBy('created_at', 'DESC')->findAll();
        
        return view('admin/donor/v_list_partial', $data);
    }

    public function detail($id)
    {
        $donor = $this->donaturModel->find($id);

        if (!$donor) {
            return redirect()
                ->to('admin/donors')
                ->with('error', 'Gagal memuat! Data donatur tidak ditemukan.');
        }

        $donations = $this->pemasukanDonasiModel
            ->select('
                pemasukan_donasi.*,
                donasi.judul as judul_donasi,
                donasi.closed_at as donasi_closed_at,
                metode_pemasukan.metode_pemasukan,
                status_donasi.status_donasi,
                status_donasi.class_color as status_color,
                keuangan.id_keuangan,
                keuangan.keterangan as keterangan_keuangan
            ')
            ->join(
                'donasi',
                'donasi.id_donasi = pemasukan_donasi.id_donasi',
                'left'
            )
            ->join(
                'metode_pemasukan',
                'metode_pemasukan.id_metode_pemasukan = pemasukan_donasi.id_metode_pemasukan',
                'left'
            )
            ->join(
                'status_donasi',
                'status_donasi.id_status_donasi = pemasukan_donasi.id_status_donasi',
                'left'
            )
            ->join(
                'keuangan',
                'keuangan.id_pemasukan_donasi = pemasukan_donasi.id_pemasukan_donasi',
                'left'
            )
            ->where('pemasukan_donasi.id_donatur', $id)
            ->where('pemasukan_donasi.id_status_donasi', 4)
            ->orderBy('pemasukan_donasi.tanggal', 'DESC')
            ->findAll();


        // =====================================================
        // RIWAYAT STATUS SETIAP PEMASUKAN
        // =====================================================

        foreach ($donations as &$donation) {

            $donation['histories'] = $this->historiStatusDonasiModel
                ->select('
                    histori_status_donasi.*,
                    status_donasi.status_donasi,
                    status_donasi.class_color
                ')
                ->join(
                    'status_donasi',
                    'status_donasi.id_status_donasi = histori_status_donasi.id_status_donasi',
                    'left'
                )
                ->where(
                    'histori_status_donasi.id_pemasukan_donasi',
                    $donation['id_pemasukan_donasi']
                )
                ->orderBy('histori_status_donasi.waktu', 'DESC')
                ->orderBy(
                    'histori_status_donasi.id_status_donasi',
                    'DESC'
                )
                ->findAll();


            // Format waktu untuk ditampilkan di modal
            foreach ($donation['histories'] as &$history) {
                $history['waktu_formatted'] = !empty($history['waktu'])
                    ? format_indo($history['waktu'], 'full_datetime')
                    : '-';
            }

            unset($history);
        }

        unset($donation);


        return view('admin/donor/v_detail', [
            'title'     => 'Detail & Riwayat Donatur',
            'donor'     => $donor,
            'donations' => $donations
        ]);
    }

    public function create() {
        $rts = $this->donaturModel->distinct()->select('rt')->where('rt IS NOT NULL', null, false)->where('rt !=', '')->orderBy('rt', 'ASC')->findColumn('rt') ?? [];
        $rws = $this->donaturModel->distinct()->select('rw')->where('rw IS NOT NULL', null, false)->where('rw !=', '')->orderBy('rw', 'ASC')->findColumn('rw') ?? [];
        $kelurahans = $this->donaturModel->distinct()->select('kelurahan')->where('kelurahan IS NOT NULL', null, false)->where('kelurahan !=', '')->orderBy('kelurahan', 'ASC')->findColumn('kelurahan') ?? [];

        return view('admin/donor/v_form', [
            'title'      => 'Tambah Donatur',
            'rts'        => $rts,
            'rws'        => $rws,
            'kelurahans' => $kelurahans
        ]);
    }

    public function edit($id) {
        $donor = $this->donaturModel->find($id);
        if (!$donor) {
            return redirect()->to('admin/donors')->with('error', 'Gagal memuat! Data donatur tidak ditemukan.');
        }

        $rts = $this->donaturModel->distinct()->select('rt')->where('rt IS NOT NULL', null, false)->where('rt !=', '')->orderBy('rt', 'ASC')->findColumn('rt') ?? [];
        $rws = $this->donaturModel->distinct()->select('rw')->where('rw IS NOT NULL', null, false)->where('rw !=', '')->orderBy('rw', 'ASC')->findColumn('rw') ?? [];
        $kelurahans = $this->donaturModel->distinct()->select('kelurahan')->where('kelurahan IS NOT NULL', null, false)->where('kelurahan !=', '')->orderBy('kelurahan', 'ASC')->findColumn('kelurahan') ?? [];

        return view('admin/donor/v_form', [
            'title'      => 'Edit Donatur',
            'donor'      => $donor,
            'rts'        => $rts,
            'rws'        => $rws,
            'kelurahans' => $kelurahans
        ]);
    }

    public function save() {
        return $this->_store();
    }

    public function update($id) {
        $donor = $this->donaturModel->find($id);
        if (!$donor) {
            return redirect()->to('admin/donors')->with('error', 'Gagal memperbarui! Data donatur tidak ditemukan.');
        }
        
        return $this->_store($id);
    }

    private function _store($id = null) {
        $currentUserId = session()->get('id_akun');

        $data = [
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'telepon'   => trim((string) $this->request->getPost('telepon')),
            'alamat'    => $this->request->getPost('alamat'),
            'rt'        => $this->request->getPost('rt'),
            'rw'        => $this->request->getPost('rw'),
            'kelurahan' => $this->request->getPost('kelurahan'),
        ];

        // Validasi nomor WhatsApp jika nomor diisi
        if (!empty($data['telepon'])) {
            $oldAccount = $id ? $this->donaturModel->find($id) : null;
            $oldPhone = trim((string) ($oldAccount['telepon'] ?? ''));
            $newPhone = $data['telepon'];

            // Cek apakah nomor baru atau nomor berubah dari sebelumnya
            $phoneChanged = !$id || ($oldPhone !== $newPhone);

            if ($phoneChanged) {
                try {
                    $checkNumber = $this->whatsappService->checkNumber($newPhone);

                    $registered = $checkNumber['data']['registered'] ?? false;
                    $valid = $checkNumber['data']['valid'] ?? false;

                    if (!$registered || !$valid) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('errors', [
                                'telepon' => 'Nomor WhatsApp tidak terdaftar atau tidak valid.'
                            ])
                            ->with('error', 'Data gagal disimpan. Silakan periksa kolom telepon.');
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'CHECK WA ERROR: ' . $e->getMessage());

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('errors', [
                            'telepon' => 'Nomor WhatsApp tidak dapat diverifikasi. Silakan coba lagi.'
                        ])
                        ->with('error', 'Data gagal disimpan. Silakan periksa kolom telepon.');
                }
            }
        }

        $expenseLama = $id ? $this->donaturModel->find($id) : null;

        if (!$id || empty($expenseLama['noreg'])) {
            $datePrefix = date('ymd');
            do {
                $randomDigits = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
                $generatedNoreg = $datePrefix . $randomDigits;
                
                // Pastikan noreg benar-benar unik di database
                $exists = $this->donaturModel->where('noreg', $generatedNoreg)->first();
            } while ($exists);

            $data['noreg'] = $generatedNoreg;
        }

        if (!$id) {
            // Data baru: tambahkan token dan created_by
            $data['token']      = bin2hex(random_bytes(32));
            $data['created_by'] = $currentUserId;
            
            // Menggunakan insert dengan model
            if (!$this->donaturModel->insert($data)) {
                return redirect()->back()->withInput()->with('errors', $this->donaturModel->errors());
            }

            $pesan = 'Data donatur baru berhasil disimpan.';
        } else {
            // Data edit: tambahkan ID primary key dan updated_by
            $data['id_donatur'] = $id;
            $data['updated_by'] = $currentUserId;
            
            // Menggunakan update dengan model
            if (!$this->donaturModel->update($id, $data)) {
                return redirect()->back()->withInput()->with('errors', $this->donaturModel->errors());
            }

            $pesan = 'Perubahan data donatur berhasil disimpan.';
        }

        return redirect()->to('admin/donors')->with('success', $pesan);
    }

    public function delete() {
        $id = $this->request->getPost('id_donatur');
        $donor = $this->donaturModel->find($id);

        if (!$donor) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data donatur tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Jejak audit sebelum soft delete
        $this->donaturModel->update($id, [
            'deleted_by' => session()->get('id_akun')
        ]);

        if ($this->donaturModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data donatur berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus data donatur.'
        ])->setStatusCode(500);
    }
}