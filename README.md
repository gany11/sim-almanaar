# SIMAS (Sistem Informasi Manajemen) - Al-Manaar

![SIMAS Banner](https://almanaar-slipi.org/uploads/carousel/Latar%20Al%20Manaar.png)

SIMAS adalah sistem informasi manajemen terintegrasi yang dirancang untuk mengelola berbagai aktivitas operasional pangkalan data dan administrasi. Sistem ini mencakup manajemen agenda, pencatatan keuangan yang akurat, serta administrasi Sumber Daya Manusia (SDM). 

Aplikasi ini mengedepankan antarmuka pengguna yang modern, responsif, dan memberikan efisiensi tinggi melalui fitur pengolahan data massal berbasis *spreadsheet* (Excel).

---

## ✨ Fitur Utama

- **📅 Manajemen Agenda:** Penjadwalan dan pengelolaan agenda secara komprehensif, mencakup pembuatan agenda rutin maupun insidental.
- **💰 Manajemen Keuangan:** Pencatatan arus kas (pemasukan & pengeluaran) dan administrasi operasional secara *real-time*.
- **👥 Manajemen SDM:** Pengelolaan data staf, pengurus, dan sumber daya manusia terkait.
- **📥 Import Data Massal:** Mendukung *import* data langsung dari format Excel (`.xlsx`) untuk mempercepat proses entri data Agenda dan Keuangan.
- **⚡ Asset Bundling & Caching:** Menggunakan integrasi Vite untuk kompilasi CSS (Tailwind) dan JavaScript yang menghasilkan *loading* aplikasi super cepat.

---

## 🚀 Teknologi yang Digunakan

Proyek ini dibangun di atas tumpukan teknologi modern untuk memastikan skalabilitas dan kenyamanan *developer*:

- **Backend:** [CodeIgniter 4](https://codeigniter.com/) (PHP Framework)
- **Frontend:** [Tailwind CSS](https://tailwindcss.com/), JavaScript Vanilla
- **Build Tool:** [Vite](https://vitejs.dev/)
- **Database:** MySQL / MariaDB
- **Hosting/Deployment:** Terkonfigurasi secara stabil dengan layanan Cloudflare untuk keamanan DNS dan dapat berjalan lancar di InfinityFree atau VPS.

---

## 🛠️ Panduan Instalasi (Development)

Ikuti instruksi di bawah ini untuk menjalankan SIMAS di mesin lokal Anda.

### Persyaratan Sistem
- PHP >= 8.1
- Composer
- Node.js & npm (untuk Vite)
- MySQL Database

### Langkah-langkah Instalasi

**1. Kloning Repository**
```bash
git clone https://github.com/gany11/sim-almanaar.git
cd sim-almanaar
```

**2. Instalasi Dependensi Backend (PHP)**
```bash
composer install
```

**3. Instalasi Dependensi Frontend (Node.js)**
```bash
npm install
```

**4. Konfigurasi Environment**
Salin file `env` menjadi `.env`.
```bash
cp env .env
```
Buka file `.env` dan atur konfigurasi berikut:
```env
# Aktifkan mode development
CI_ENVIRONMENT = development

# Konfigurasi Database
database.default.hostname = localhost
database.default.database = nama_database_simas
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

**5. Migrasi Database**
Jalankan migrasi untuk men-generate tabel database secara otomatis (termasuk tabel Agenda, SDM, Keuangan).
```bash
php spark migrate
```

**6. Jalankan Server Development**
Proyek ini membutuhkan dua server lokal yang berjalan secara paralel (satu untuk PHP, satu untuk Vite). Buka dua jendela terminal/command prompt:

**Terminal 1: Menjalankan Vite (Asset Bundler)**
```bash
npm run dev
```

**Terminal 2: Menjalankan CodeIgniter Server**
```bash
php spark serve
```

Aplikasi sekarang dapat diakses melalui browser pada `http://localhost:8080`.

---

## 📁 Struktur Direktori Utama

- `app/` - Direktori utama MVC CodeIgniter (Controllers, Models, Views).
- `public/` - Dokumen *root* yang dapat diakses publik (CSS, JS, Images, Uploads).
  - `public/build/` - Hasil kompilasi Vite (jangan diubah manual).
  - `public/assets/templates/` - Berisi file *template* Excel untuk fitur import.
- `src/` - *Source files* untuk Tailwind dan JavaScript sebelum di-*compile*.

> **Penting:** Pastikan folder `public/uploads` dan `public/build/uploads` tidak di-commit ke Git agar tidak memenuhi *repository* (sudah diatur di `.gitignore`).

---

## 👨‍💻 Pengembang

Dikembangkan dan di-maintain oleh **Fany Andisa**.