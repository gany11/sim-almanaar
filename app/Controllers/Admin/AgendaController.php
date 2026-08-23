<?php

namespace App\Controllers\Admin;

use App\Models\AgendaModel;
use App\Models\SdmModel;
use App\Models\KategoriAgendaModel;
use App\Models\KategoriSdmModel;
use App\Models\KeteranganWaktuModel;
use App\Models\SdmAgendaModel;

use IslamicNetwork\PrayerTimes\PrayerTimes;
use IslamicNetwork\PrayerTimes\Method;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AgendaController extends BaseController
{
    protected $agendaModel;
    protected $sdmModel;
    protected $katAgendaModel;
    protected $katSdmModel;
    protected $ketWaktuModel;
    protected $sdmAgendaModel;

    public function __construct()
    {
        $this->agendaModel    = new AgendaModel();
        $this->sdmModel       = new SdmModel();
        $this->katAgendaModel = new KategoriAgendaModel();
        $this->katSdmModel    = new KategoriSdmModel();
        $this->ketWaktuModel  = new KeteranganWaktuModel();
        $this->sdmAgendaModel = new SdmAgendaModel();
    }

    public function index()
    {
        return view('admin/agenda/v_index', [
            'title'      => 'Manajemen Agenda',
            'categories' => $this->katAgendaModel->findAll()
        ]);
    }

    public function list()
    {
        $id_kategori = $this->request->getPost('id_kategori_agenda');
        
        $builder = $this->agendaModel->select('agenda.*, kategori_agenda.nama_kategori, kategori_agenda.class_color')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda');

        if (!empty($id_kategori)) {
            $builder->where('agenda.id_kategori_agenda', $id_kategori);
        }

        $agendaRaw = $builder->orderBy('agenda.waktu_mulai', 'DESC')->findAll();

        $agendaFinal = [];
        foreach ($agendaRaw as $row) {
            $pengisi = $this->sdmAgendaModel->select('sdm.nama')
                ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                ->where('id_agenda', $row['id_agenda'])
                ->findAll();
            
            $row['nama_pengisi'] = !empty($pengisi) ? array_column($pengisi, 'nama') : [];
            $agendaFinal[] = $row;
        }

        $data['agenda'] = $agendaFinal;
        return view('admin/agenda/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/agenda/v_form', [
            'title'      => 'Tambah Agenda Baru',
            'categories' => $this->katAgendaModel->findAll(),
            'sdm_roles'  => $this->katSdmModel->findAll(),
            'times'      => $this->ketWaktuModel->findAll(),
            'all_sdm'    => $this->sdmModel->findAll(),
        ]);
    }

    public function edit($id)
    {
        $agenda = $this->agendaModel->find($id);
        if (!$agenda) return redirect()->to('admin/agenda')->with('error', 'Agenda tidak ditemukan.');

        // Ambil relasi SDM yang sudah ada
        $currentSdm = $this->sdmAgendaModel->where('id_agenda', $id)->findAll();

        return view('admin/agenda/v_form', [
            'title'      => 'Edit Agenda',
            'agenda'     => $agenda,
            'currentSdm' => $currentSdm,
            'categories' => $this->katAgendaModel->findAll(),
            'sdm_roles'  => $this->katSdmModel->findAll(),
            'times'      => $this->ketWaktuModel->findAll(),
            'all_sdm'    => $this->sdmModel->findAll(),
        ]);
    }

    public function save() { return $this->_store(); }
    public function update($id) { return $this->_store($id); }

    protected function _store($id = null)
    {
        $rules = $this->agendaModel->validationRules;

        $idKategoriAgenda = $this->request->getPost('id_kategori_agenda');

        if ($idKategoriAgenda == 1) {
            $rules['tema'] = [
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'Tema agenda maksimal 255 karakter.',
                ]
            ];
        }

        $errors = [];

        // Validasi bawaan CI4
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
        }

        // Validasi Custom (SDM)
        $sdmIds   = $this->request->getPost('sdm_id') ?? [];
        $sdmRoles = $this->request->getPost('sdm_role') ?? [];

        // Cek jika array benar-benar kosong ATAU hanya ada 1 baris tapi KEDUANYA (nama & peran) kosong
        if (empty($sdmIds) || (count($sdmIds) === 1 && empty($sdmIds[0]) && empty($sdmRoles[0]))) {
            $errors['sdm'] = 'Minimal harus ada satu pengisi.';
        } else {
            foreach ($sdmIds as $i => $sdmId) {
                // Jika salah satu (nama atau peran) kosong pada baris manapun
                if (empty($sdmId) || empty($sdmRoles[$i])) {
                    $errors['sdm'] = 'Semua pengisi beserta perannya wajib dilengkapi.';
                    break; 
                }
            }
        }

        // Satu kali redirect
        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }

        $currentUserId = session()->get('id_akun');
        $startStr = $this->request->getPost('waktu_mulai');
        $endStr   = $this->request->getPost('waktu_selesai');

        if ($idKategoriAgenda == 1) {
            // Ambil tanggal saja
            $tanggal = date('Y-m-d', strtotime($startStr));

            // Pastikan hari Jumat
            if (date('N', strtotime($tanggal)) != 5) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', [
                        'waktu_mulai' => 'Sholat Jumat hanya dapat dijadwalkan pada hari Jumat.'
                    ]);
            }

            // $latitude  = -6.190834662826311;
            // $longitude = 106.80125993207903;
            // $timezone  = 'Asia/Jakarta';

            // $pt = new PrayerTimes(Method::METHOD_SINGAPORE);

            // $date = new \DateTime($tanggal, new \DateTimeZone('Asia/Jakarta'));

            // $times = $pt->getTimes(
            //     $date,
            //     $latitude,
            //     $longitude
            // );

            // $dhuhr = $times['Dhuhr']; 
            // $startStr = $tanggal . ' ' . $dhuhr;
            // $endStr = date('Y-m-d H:i', strtotime($startStr . ' +1 hour'));
        }

        if (empty($endStr)) {
            $endStr = date('Y-m-d H:i', strtotime($startStr . ' +1 hour'));
        }

        if (strtotime($endStr) <= strtotime($startStr)) {
            return redirect()->back()->withInput()->with('errors', ['waktu_selesai' => 'Waktu selesai harus lebih besar dari waktu mulai.']);
        }

        // --- MULAI: VALIDASI DUPLIKASI AGENDA ---
        $formattedMulai = date('Y-m-d H:i:s', strtotime($startStr));
        
        $cekDuplikasi = $this->agendaModel
            ->where('id_kategori_agenda', $idKategoriAgenda)
            ->where('waktu_mulai', $formattedMulai);
            
        // Jika proses UPDATE, kecualikan ID yang sedang diedit dari pengecekan
        if ($id) {
            $cekDuplikasi->where('id_agenda !=', $id);
        }

        if ($cekDuplikasi->first()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan! Agenda dengan kategori dan waktu mulai yang sama sudah terdaftar di sistem.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $dataAgenda = [
            'id_kategori_agenda'         => $idKategoriAgenda,
            'tema'                       => ($idKategoriAgenda == 1 && trim($this->request->getPost('tema')) === '')? '-' : $this->request->getPost('tema'),
            'judul'                      => $this->request->getPost('judul'),
            'deskripsi'                  => $this->request->getPost('deskripsi'),
            'tempat'                     => $this->request->getPost('tempat'),
            'waktu_mulai'                => date('Y-m-d H:i:s', strtotime($startStr)),
            'waktu_selesai'              => date('Y-m-d H:i:s', strtotime($endStr)),
            'id_keterangan_waktu_mulai'   => $this->request->getPost('id_keterangan_waktu_mulai') ?: null,
            'id_keterangan_waktu_selesai' => $this->request->getPost('id_keterangan_waktu_selesai') ?: null,
            'method'                      => 'manual',
        ];
                
        if ($id) {
            // Logika Update
            $dataAgenda['updated_by'] = $currentUserId;
            $this->agendaModel
                ->skipValidation(true)
                ->update($id, $dataAgenda);
            $agendaID = $id;
        } else {
            // Logika Create
            $dataAgenda['created_by'] = $currentUserId;
            $agendaID = $this->agendaModel
                            ->skipValidation(true)
                            ->insert($dataAgenda);
        }

        if ($agendaID) {
            $this->sdmAgendaModel->where('id_agenda', $agendaID)->delete();
            if ($sdmIds) {
                foreach ($sdmIds as $key => $val) {
                    if (empty($val)) continue;
                    $finalSdmId = $val;

                    if (!is_numeric($val)) {
                        $newSdm = $this->sdmModel->where('nama', $val)->first();
                        if ($newSdm) {
                            $finalSdmId = $newSdm['id_sdm'];
                        } else {
                            $finalSdmId = $this->sdmModel->insert([
                                'nama'       => $val,
                                'created_by' => $currentUserId
                            ]);
                        }
                    }

                    $this->sdmAgendaModel->insert([
                        'id_agenda'       => $agendaID,
                        'id_sdm'          => $finalSdmId,
                        'id_kategori_sdm' => $sdmRoles[$key]
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
        }

        return redirect()->to('admin/agenda')->with('success', 'Agenda berhasil disimpan!');
    }

    public function delete()
    {
        $id = $this->request->getPost('id_agenda');
        $agenda = $this->agendaModel->find($id);

        if (!$agenda) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data agenda tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $this->agendaModel->update($id, [
            'deleted_by' => session()->get('id_akun')
        ]);

        if ($this->agendaModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Agenda berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus agenda.'
        ])->setStatusCode(500);
    }

    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file->isValid()) {
            return redirect()->to('admin/agenda')->with('error', 'File tidak valid.');
        }

        // Menggunakan class dari namespace yang sudah di-use
        $reader = new Xlsx(); 
        $spreadsheet = $reader->load($file->getTempName());
        $dataRaw = $spreadsheet->getActiveSheet()->toArray();

        // Validasi Versi Template
        $templateVersion = trim($spreadsheet->getActiveSheet()->getCell('K3')->getValue());
        if ($templateVersion !== '26.8.23') {
            return redirect()->to('admin/agenda')->with('error', 'Gunakan file template terbaru!');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $importErrors = [];
        $agendaTerproses = [];
        $skippedCount = 0;
        $countAgenda = 0;
        $countSdm = 0;

        $currentUserId = session()->get('id_akun');

        for ($i = 4; $i < count($dataRaw); $i++) {
            $row = $dataRaw[$i];
            
            if (empty(array_filter($row))) continue;

            $rowNumber = $i + 1;
            $lineErrors = [];

            // --- 1. Mapping Kolom Excel ---
            $namaKategori     = trim((string)$row[0]); // Kolom A
            $tema             = trim((string)$row[1]); // Kolom B
            $judul            = trim((string)$row[2]); // Kolom C
            $deskripsi        = trim((string)$row[3]); // Kolom D
            $tempat           = trim((string)$row[4]); // Kolom E
            $waktuMulaiRaw    = trim((string)$row[5]); // Kolom F
            $waktuSelesaiRaw  = trim((string)$row[6]); // Kolom G
            $ketWaktuMulai    = trim((string)$row[7]); // Kolom H
            $ketWaktuSelesai  = trim((string)$row[8]); // Kolom I
            $namaSdm          = trim((string)$row[9]); // Kolom J
            $peranSdm         = trim((string)$row[10]); // Kolom K

            // --- 2. Validasi Input Dasar ---
            if (empty($namaKategori)) $lineErrors[] = "Kategori kosong";
            if (empty($waktuMulaiRaw)) $lineErrors[] = "Waktu Mulai kosong";
            if (empty($namaSdm)) $lineErrors[] = "Nama SDM kosong";
            if (empty($peranSdm)) $lineErrors[] = "Peran SDM kosong";
            
            // Pengecualian wajib Tema untuk Sholat Jumat
            $isSholatJumat = (strcasecmp($namaKategori, 'Sholat Jumat') === 0 || strcasecmp($namaKategori, 'Sholat Jum\'at') === 0);
            if (empty($tema) && !$isSholatJumat) {
                $lineErrors[] = "Tema wajib diisi selain untuk Sholat Jumat";
            }

            if (!empty($lineErrors)) {
                $importErrors[] = "Baris {$rowNumber}: " . implode(', ', $lineErrors);
                continue;
            }

            // --- 3. Lookup Master Data ---
            // Cari ID Kategori Agenda (Pakai $this->katAgendaModel sesuai construct Anda)
            $kategoriAgenda = $this->katAgendaModel->where('nama_kategori', $namaKategori)->first();
            if (!$kategoriAgenda) {
                $importErrors[] = "Baris {$rowNumber}: Kategori '{$namaKategori}' tidak ditemukan di sistem.";
                continue;
            }
            $idKategoriAgenda = $kategoriAgenda['id_kategori_agenda'];

            // Cari ID Peran SDM (Pakai $this->katSdmModel)
            $kategoriSdm = $this->katSdmModel->where('kategori', $peranSdm)->first();
            if (!$kategoriSdm) {
                $importErrors[] = "Baris {$rowNumber}: Peran SDM '{$peranSdm}' tidak ditemukan di sistem.";
                continue;
            }
            $idKategoriSdm = $kategoriSdm['id_kategori_sdm'];

            // Cari Keterangan Waktu (Pakai $this->ketWaktuModel)
            $idKetWaktuMulai = null;
            if (!empty($ketWaktuMulai)) {
                $ket = $this->ketWaktuModel->where('keterangan', $ketWaktuMulai)->first();
                $idKetWaktuMulai = $ket ? $ket['id_keterangan_waktu'] : null;
            }

            $idKetWaktuSelesai = null;
            if (!empty($ketWaktuSelesai)) {
                $ket = $this->ketWaktuModel->where('keterangan', $ketWaktuSelesai)->first();
                $idKetWaktuSelesai = $ket ? $ket['id_keterangan_waktu'] : null;
            }

            // --- 4. Proses Datetime & Nilai Default ---
            $tempat = empty($tempat) ? "Ruang Utama Masjid Al-Manaar Slipi" : $tempat;

            // Tangani format Tanggal Excel
            if (is_numeric($waktuMulaiRaw)) {
                $waktuMulai = Date::excelToDateTimeObject($waktuMulaiRaw)->format('Y-m-d H:i:s');
            } else {
                $waktuMulai = date('Y-m-d H:i:s', strtotime($waktuMulaiRaw));
            }

            // Atur Waktu Selesai Default (+1 Jam)
            if (empty($waktuSelesaiRaw)) {
                $waktuSelesai = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($waktuMulai)));
            } else {
                if (is_numeric($waktuSelesaiRaw)) {
                    $waktuSelesai = Date::excelToDateTimeObject($waktuSelesaiRaw)->format('Y-m-d H:i:s');
                } else {
                    $waktuSelesai = date('Y-m-d H:i:s', strtotime($waktuSelesaiRaw));
                }
            }

            if (strtotime($waktuSelesai) <= strtotime($waktuMulai)) {
                $importErrors[] = "Baris {$rowNumber}: Waktu selesai harus lebih besar dari waktu mulai.";
                continue;
            }

            if ($idKategoriAgenda == 1) {
                if (date('N', strtotime($waktuMulai)) != 5) {
                    $importErrors[] = "Baris {$rowNumber}: Sholat Jumat hanya dapat dijadwalkan pada hari Jumat.";
                    continue;
                }
            }

            // --- 5. Logika Insert/Get Agenda (Grouping & Cek Duplikasi DB) ---
            $groupKey = $idKategoriAgenda . '_' . $waktuMulai;
            
            if (!isset($agendaTerproses[$groupKey])) {
                
                // CEK DUPLIKASI KE DATABASE
                $existingAgenda = $this->agendaModel->where([
                    'id_kategori_agenda' => $idKategoriAgenda,
                    'waktu_mulai'        => $waktuMulai
                ])->first();

                if ($existingAgenda) {
                    // JIKA SUDAH ADA: Tandai group ini agar di-skip
                    $agendaTerproses[$groupKey] = 'SKIPPED';
                    $skippedCount++; // Hitung 1 agenda yang dilewati
                } else {
                    // JIKA BELUM ADA: Insert agenda baru
                    $dataAgenda = [
                        'id_kategori_agenda'          => $idKategoriAgenda,
                        'tema'                        => empty($tema) ? '-' : $tema,
                        'judul'                       => empty($judul) ? null : $judul,
                        'deskripsi'                   => empty($deskripsi) ? null : $deskripsi,
                        'tempat'                      => $tempat,
                        'waktu_mulai'                 => $waktuMulai,
                        'waktu_selesai'               => $waktuSelesai,
                        'id_keterangan_waktu_mulai'   => $idKetWaktuMulai,
                        'id_keterangan_waktu_selesai' => $idKetWaktuSelesai,
                        'created_by'                  => $currentUserId,
                        'method'                      => 'import',
                    ];
                    
                    $this->agendaModel->skipValidation(true)->insert($dataAgenda);
                    $agendaTerproses[$groupKey] = $this->agendaModel->insertID();
                    $countAgenda++; 
                }
            }
            
            $idAgenda = $agendaTerproses[$groupKey];

            // Jika agenda ditandai 'SKIPPED', hentikan proses di baris Excel ini (jangan insert SDM-nya)
            if ($idAgenda === 'SKIPPED') {
                continue; 
            }

            // --- 6. Logika Insert/Get SDM ---
            $sdmExist = $this->sdmModel->where('nama', $namaSdm)->first();
            if ($sdmExist) {
                $idSdm = $sdmExist['id_sdm'];
            } else {
                $this->sdmModel->insert([
                    'nama'       => $namaSdm,
                    'created_by' => $currentUserId
                ]);
                $idSdm = $this->sdmModel->insertID();
            }

            // --- 7. Insert Relasi SDM Agenda ---
            $this->sdmAgendaModel->insert([
                'id_agenda'       => $idAgenda,
                'id_sdm'          => $idSdm,
                'id_kategori_sdm' => $idKategoriSdm
            ]);
            $countSdm++;
        }

        // --- 8. Eksekusi Akhir & Redirect ---
        if (!empty($importErrors)) {
            $db->transRollback();
            return redirect()->to('admin/agenda')->with('error_list', $importErrors);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('admin/agenda')->with('error', 'Gagal memproses import data ke database.');
        }

        $msg = "Berhasil import {$countAgenda} agenda baru dengan total {$countSdm} penugasan SDM.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} agenda dilewati karena sudah ada di database).";
        }
        
        return redirect()->to('admin/agenda')->with('success', $msg);
    }
}
