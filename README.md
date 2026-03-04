# Klinik Laktasi - PHP MySQL

Sistem Manajemen Klinik Laktasi berbasis PHP dan MySQL.

## 📋 Persyaratan Sistem

- **Web Server**: Apache (Laragon / XAMPP / WAMP)
- **PHP**: Versi 7.4 atau lebih tinggi
- **Database**: MySQL 5.7+ atau MariaDB
- **Browser**: Chrome, Firefox, Edge (versi terbaru)

## 🚀 Instalasi dengan Laragon

### 1. Setup Database

1. Buka Laragon dan pastikan Apache & MySQL sudah running
2. Buka phpMyAdmin (http://localhost/phpmyadmin)
3. Buat database baru dengan nama: `klinik_laktasi`
4. Import file SQL:
   - Buka tab "Import" di phpMyAdmin
   - Pilih file `database/schema.sql`
   - Klik "Go" untuk mengimport

### 2. Konfigurasi Database

File konfigurasi database sudah ada di `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'klinik_laktasi');
```

Sesuaikan jika konfigurasi database Anda berbeda.

### 3. Setup Database

**Untuk database yang sudah ada:**

1. Buka browser dan akses: `http://localhost/klinik-laktasi2/setup-database.php`
2. Script akan otomatis menambahkan kolom yang hilang
3. Jika sudah berhasil, klik link ke Dashboard

**Untuk database baru:**

1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru dengan nama: `klinik_laktasi`
3. Import file SQL:
   - Buka tab "Import" di phpMyAdmin
   - Pilih file `database/schema.sql`
   - Klik "Go" untuk mengimport

### 4. Konfigurasi Base URL

Buka file `config/config.php` dan sesuaikan BASE_URL dengan struktur folder Anda:

```php
// Jika menggunakan localhost dengan subfolder:
define('BASE_URL', 'http://localhost/klinik-laktasi2');

// ATAU jika menggunakan virtual host:
// define('BASE_URL', 'http://klinik-laktasi.test');
```

### 5. Akses Aplikasi

**Opsi A: Menggunakan localhost (default)**
```
http://localhost/klinik-laktasi2
```

**Opsi B: Menggunakan Virtual Host (recommended)**
1. Buka Laragon
2. Klik **Menu** → **Apache** → **hosts**
3. Tambahkan baris ini:
   ```
   127.0.0.1  klinik-laktasi.test
   ```
4. Klik **Menu** → **Apache** → **Restart**
5. Akses: `http://klinik-laktasi.test`

### 6. Login

Login dengan kredensial default:
- **Email**: admin@kliniklaktasi.com
- **Password**: password

## 📁 Struktur Folder

```
klinik-laktasi2/
├── api/                    # API endpoints untuk AJAX
│   ├── pasien.php
│   ├── registrasi.php
│   └── user.php
├── assets/
│   └── css/
│       └── style.css       # Stylesheet utama
├── components/             # Komponen UI yang dapat digunakan kembali
│   ├── header.php
│   ├── sidebar.php
│   └── StatCard.php
├── config/
│   └── database.php        # Konfigurasi database
├── database/
│   └── schema.sql          # Schema database MySQL
├── includes/
│   └── auth.php            # Fungsi autentikasi & helper
├── layouts/
│   └── dashboard.php       # Layout utama dashboard
├── pages/                  # Halaman-halaman aplikasi
│   ├── pasien.php
│   ├── antrian.php
│   ├── registrasi.php
│   ├── asesmen.php
│   ├── kajian-riwayat-menyusui.php
│   ├── kunjungan.php
│   ├── manajemen-user.php
│   ├── setting.php
│   ├── rekam.php
│   ├── farmasi.php
│   ├── obat.php
│   ├── tarif.php
│   └── pembayaran.php
├── index.php               # Dashboard utama
├── login.php               # Halaman login
└── logout.php              # Logout handler
```

## 🔐 Fitur Utama

### 1. Authentication & Authorization
- Login/Logout system
- Session management
- Role-based access (ADMIN, medis)
- CSRF protection

### 2. Manajemen Pasien
- Daftar pasien dengan No. RM otomatis
- CRUD pasien (Create, Read, Update, Delete)
- Pencarian pasien
- Edit data pasien

### 3. Registrasi & Antrian
- Registrasi pasien baru
- Manajemen antrian perawatan
- Update data registrasi
- Hapus registrasi

### 4. Asesmen Konseling Laktasi
- Form asesmen lengkap
- Data ibu dan bayi
- Riwayat menyusui
- Pemeriksaan fisik
- Diagnosis dan rencana tindak lanjut

### 5. Kajian Riwayat Menyusui
- Formulir pengkajian komprehensif
- Pemberian makan bayi
- Kesehatan & perilaku bayi
- Kehamilan dan kelahiran
- Kondisi ibu & KB
- Situasi keluarga & sosial

### 6. Manajemen User (Admin Only)
- Tambah user baru
- Hapus user
- Filter dan pencarian user
- Role management

### 7. Rekam Medis
- Lihat semua asesmen
- Riwayat kunjungan pasien

## 🎨 Fitur UI/UX

- Responsive design (mobile-friendly)
- Modern gradient theme
- Modal dialogs untuk form input
- Alert notifications (success, error, warning, info)
- Smooth animations dan transitions
- Custom scrollbar
- Chart.js integration untuk dashboard

## 🛠️ Fungsi Helper yang Tersedia

### Database Functions
```php
getDbConnection()      // Mendapatkan koneksi PDO
dbQuery($sql, $params) // Execute query dan fetch all
dbQueryOne($sql)       // Execute query dan fetch single row
dbExecute($sql)        // Execute insert/update/delete
dbLastInsertId()       // Mendapatkan ID terakhir yang di-insert
```

### Authentication Functions
```php
isLoggedIn()           // Cek apakah user sudah login
getCurrentUser()       // Mendapatkan data user yang login
requireAuth()          // Redirect ke login jika belum login
isAdmin()              // Cek apakah user adalah admin
```

### Utility Functions
```php
sanitize($data)        // Sanitize input data
formatDateIndonesian($date)  // Format tanggal ke bahasa Indonesia
calculateAge($birthDate)     // Hitung usia dari tanggal lahir
generateRegistrationNumber() // Generate no. registrasi otomatis
generateMedicalRecordNumber() // Generate no. RM otomatis
redirect($url, $message, $type) // Redirect dengan flash message
jsonResponse($data, $statusCode) // Return JSON response
```

## 📝 Default User

| Role  | Email                    | Password |
|-------|--------------------------|----------|
| Admin | admin@kliniklaktasi.com  | password |

**Penting**: Segera ubah password default setelah instalasi pertama!

## 🔧 Troubleshooting

### Error: Database connection failed
- Pastikan MySQL service sudah running di Laragon
- Periksa kredensial database di `config/database.php`
- Pastikan database `klinik_laktasi` sudah dibuat

### Error: Page not found
- Pastikan URL path sudah benar
- Periksa konfigurasi virtual host jika menggunakan custom domain

### Error: Session issues
- Pastikan folder session PHP memiliki permission yang tepat
- Periksa konfigurasi session di php.ini

## 📊 Database Tables

1. **users** - Data pengguna sistem
2. **pasien** - Data pasien dengan No. RM
3. **registrasi** - Data registrasi/antrian pasien
4. **kajian_riwayat_menyusui** - Data kajian riwayat menyusui
5. **asesmen** - Data asesmen konseling laktasi
6. **kunjungan** - Data riwayat kunjungan pasien

## 🚀 Pengembangan Selanjutnya

Untuk menambahkan fitur baru:

1. **Tambah halaman baru**: Buat file PHP di folder `pages/`
2. **Tambah API endpoint**: Buat file PHP di folder `api/`
3. **Tambah komponen**: Buat file PHP di folder `components/`
4. **Update database**: Tambahkan migration di folder `database/`

## 📄 License

Sistem ini dibuat untuk Klinik Laktasi IKMI (Ikatan Konselor Menyusui Indonesia).

## 👨‍💻 Developer

Dikembangkan dengan ❤️ untuk meningkatkan pelayanan klinik laktasi di Indonesia.

---

**© 2026 Klinik Laktasi IKMI**
