<?php

namespace App\Controllers\Admin;

use App\Models\KeuanganModel;
use App\Models\KategoriKeuanganModel;
use App\Models\LaporanMingguanModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FinanceController extends BaseController
{
    protected $keuanganModel;
    protected $kategoriModel;
    protected $laporanModel;

    public function __construct() {
        $this->keuanganModel = new KeuanganModel();
        $this->kategoriModel = new KategoriKeuanganModel();
        $this->laporanModel  = new LaporanMingguanModel();
    }

    public function index() {
        return view('admin/finance/v_index', [
            'title'    => 'Keuangan Rutin',
            'kategori' => $this->kategoriModel->findAll(),
            'summaryKeuangan' => $this->keuanganModel->getSummaryPerKategori()
        ]);
    }

    public function list() {
        $now = date('Y-m-d H:i:s');
        
        $currentReport = $this->laporanModel
            ->where('started_at <=', $now)
            ->where('ended_at >=', $now)
            ->first();

        $builder = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, kategori_keuangan.class_color, creator.nama as creator_name, editor.nama as editor_name')
                    ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan')
                    ->join('akun as creator', 'creator.id_akun = keuangan.created_by', 'left')
                    ->join('akun as editor', 'editor.id_akun = keuangan.updated_by', 'left');

        if ($currentReport) {
            $builder->where('keuangan.created_at >=', $currentReport['started_at'])
                    ->where('keuangan.created_at <=', $currentReport['ended_at']);
        } else {
            $builder->where('keuangan.id_keuangan', 0); 
        }

        $cat  = $this->request->getPost('id_kategori_keuangan');
        $type = $this->request->getPost('jenis');
        if ($cat) $builder->where('keuangan.id_kategori_keuangan', $cat);
        if ($type) $builder->where('keuangan.jenis', $type);

        $data['current_report'] = $currentReport;
        $data['routine'] = $builder->orderBy('keuangan.created_at', 'ASC')->findAll();

        $data['summaryKeuangan'] = $this->keuanganModel->getSummaryPerKategori();

        $data['history'] = $this->laporanModel
            ->where('ended_at <', $now)
            ->orderBy('ended_at', 'DESC')
            ->findAll();

        return view('admin/finance/v_list_partial', $data);
    }

    public function create() {
        return view('admin/finance/v_form', [
            'title'    => 'Tambah Transaksi',
            'kategori' => $this->kategoriModel->findAll()
        ]);
    }

    public function edit($id) {
        $keuangan = $this->keuanganModel->find($id);

        if (!$keuangan) {
            return redirect()->to('admin/finance/routine')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->_isCurrentPeriod($keuangan['created_at'])) {
            return redirect()->to('admin/finance/routine')->with('error', 'Akses ditolak! Transaksi pada laporan yang sudah lewat tidak dapat diubah.');
        }

        return view('admin/finance/v_form', [
            'title'    => 'Edit Transaksi',
            'keuangan' => $keuangan,
            'kategori' => $this->kategoriModel->findAll()
        ]);
    }

    public function save() { 
        return $this->_store(); 
    }

    public function update($id) { 
        $keuangan = $this->keuanganModel->find($id);

        if (!$keuangan) {
            return redirect()->to('admin/finance/routine')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->_isCurrentPeriod($keuangan['created_at'])) {
            return redirect()->to('admin/finance/routine')->with('error', 'Akses ditolak! Transaksi pada laporan yang sudah lewat tidak dapat diubah.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null) {
        $file = $this->request->getFile('bukti');

        if ($file && $file->getError() === UPLOAD_ERR_INI_SIZE) {
            return redirect()->back()->withInput()->with('errors', [
                'bukti' => 'Ukuran file terlalu besar (melebihi batas limit server).'
            ]);
        }

        $rules = $this->keuanganModel->validationKeuangan;

        $jumlahRaw = $this->request->getPost('jumlah');
        $jumlahClean = str_replace('.', '', $jumlahRaw ?? '0');
        
        $_POST['jumlah'] = $jumlahClean;

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldData = $id ? $this->keuanganModel->find($id) : null;
        $fileName = $oldData ? $oldData['bukti'] : null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move('uploads/keuangan', $fileName);
            
            if ($oldData && $oldData['bukti'] && file_exists('uploads/keuangan/' . $oldData['bukti'])) {
                @unlink('uploads/keuangan/' . $oldData['bukti']);
            }
        }

        $nowTime = date('Y-m-d H:i:s');
        $this->_ensureReportExists($nowTime);

        $saveData = [
            'id_kategori_keuangan' => $this->request->getPost('id_kategori_keuangan'),
            'tanggal'              => $this->request->getPost('tanggal'),
            'jumlah'               => $jumlahClean,
            'jenis'                => $this->request->getPost('jenis'),
            'keterangan'           => $this->request->getPost('keterangan'),
            'bukti'                => $fileName,
        ];

        $currentUserId = session()->get('id_akun');

        if (!$id) {
            $saveData['created_by'] = $currentUserId;
            $this->keuanganModel->insert($saveData);
            $msg = 'Data transaksi berhasil dicatat.';
        } else {
            $saveData['updated_by'] = $currentUserId;
            $this->keuanganModel->update($id, $saveData);
            $msg = 'Perubahan transaksi berhasil disimpan.';
        }

        return redirect()->to('admin/finance/routine')->with('success', $msg);
    }

    private function _ensureReportExists($time) {
        $check = $this->laporanModel->where('started_at <=', $time)->where('ended_at >=', $time)->first();
        if (!$check) {
            // Hitung Jumat Terdekat (Start)
            $start = date('Y-m-d 00:00:00', strtotime('last friday', strtotime($time)));
            if (date('N', strtotime($time)) == 5) $start = date('Y-m-d 00:00:00', strtotime($time));
            
            $end = date('Y-m-d 23:59:59', strtotime($start . ' +6 days'));
            
            // Hitung Pekan ke- (P1, P2, dst)
            $dayOfMonth = date('j', strtotime($start));
            $weekNum    = ceil($dayOfMonth / 7);
            $monthName  = date('F', strtotime($start));
            
            $title = "Laporan Keuangan " . date('d/M/Y', strtotime($start)) . " - " . date('d/M/Y', strtotime($end)) . " (P{$weekNum} {$monthName})";

            $this->laporanModel->insert([
                'judul'      => $title,
                'started_at' => $start,
                'ended_at'   => $end,
                'created_by' => session()->get('id_akun')
            ]);
        }
    }

    private function _isCurrentPeriod($createdAt) {
        $now = date('Y-m-d H:i:s');
        $currentReport = $this->laporanModel
            ->where('started_at <=', $now)
            ->where('ended_at >=', $now)
            ->first();

        if (!$currentReport) return false;

        return ($createdAt >= $currentReport['started_at'] && $createdAt <= $currentReport['ended_at']);
    }

    public function delete() {
        $id = $this->request->getPost('id_keuangan');
        $keuangan = $this->keuanganModel->find($id);

        if (!$keuangan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        if (!$this->_isCurrentPeriod($keuangan['created_at'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaksi lama tidak boleh dihapus demi integritas laporan.']);
        }

        $this->keuanganModel->update($id, ['deleted_by' => session()->get('id_akun')]);
        $this->keuanganModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
    }
}
