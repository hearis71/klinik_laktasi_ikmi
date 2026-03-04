# Deploy ke Railway

## 🚀 Langkah-langkah Deploy

### 1. Persiapan Repository

Pastikan repository GitHub Anda sudah berisi:
- ✅ `composer.json` - Dependency management
- ✅ `Dockerfile` - Konfigurasi build
- ✅ `railway.json` - Konfigurasi Railway
- ✅ `.gitignore` - File yang tidak perlu di-track

### 2. Deploy ke Railway

1. **Login ke Railway**
   - Buka https://railway.app
   - Login dengan akun GitHub Anda

2. **Buat Project Baru**
   - Klik **"New Project"**
   - Pilih **"Deploy from GitHub repo"**
   - Pilih repository `klinik-laktasi2`

3. **Konfigurasi Environment Variables**

   Di Railway Dashboard, buka **Variables** dan tambahkan:

   ```
   DB_HOST=<host-database-anda>
   DB_USER=<user-database>
   DB_PASS=<password-database>
   DB_NAME=<nama-database>
   
   BASE_URL=https://<your-project>.up.railway.app
   ```

   > **Catatan**: Railway menyediakan MySQL add-on. Atau gunakan database eksternal seperti PlanetScale, Aiven, dll.

4. **Deploy**
   - Railway akan otomatis build menggunakan Dockerfile
   - Tunggu proses build selesai (~2-5 menit)
   - Aplikasi akan otomatis deploy

### 3. Setup Database

Setelah deploy berhasil:

1. **Jika menggunakan Railway MySQL:**
   - Buka tab **"New"** → **"Add MySQL"**
   - Tunggu hingga database siap
   - Copy environment variables yang dihasilkan
   - Paste ke Variables project Anda

2. **Jalankan setup database:**
   - Buka `https://<your-project>.up.railway.app/setup-database.php`
   - Script akan membuat tabel yang diperlukan

3. **Login**
   - Email: `admin@kliniklaktasi.com`
   - Password: `password`

## 🔧 Environment Variables

| Variable | Deskripsi | Contoh |
|----------|-----------|--------|
| `DB_HOST` | Host database | `mysql.railway.internal` |
| `DB_USER` | Username database | `root` |
| `DB_PASS` | Password database | `your-password` |
| `DB_NAME` | Nama database | `klinik_laktasi` |
| `BASE_URL` | URL aplikasi | `https://project.up.railway.app` |

## 📝 Catatan Penting

### Composer
- `vendor/` folder **tidak** di-commit (sudah ada di `.gitignore`)
- Railway akan install dependencies otomatis saat build via Dockerfile
- File `composer.lock` juga di-ignore untuk fleksibilitas

### Database
- Railway menyediakan MySQL add-on gratis untuk testing
- Untuk production, pertimbangkan database eksternal yang lebih stabil
- Pastikan koneksi database aman dengan password yang kuat

### File Persistence
- Railway adalah platform ephemeral (file tidak persistent)
- Jangan simpan file upload di filesystem lokal
- Gunakan storage eksternal (S3, Google Cloud Storage, dll) untuk file uploads

### Logs & Monitoring
- Buka tab **"Logs"** di Railway untuk melihat log aplikasi
- Gunakan tab **"Metrics"** untuk monitoring resource

## 🐛 Troubleshooting

### Build Failed
- Cek log di Railway untuk error detail
- Pastikan `composer.json` valid
- Verifikasi Dockerfile tidak ada error syntax

### Database Connection Failed
- Pastikan environment variables sudah benar
- Cek apakah MySQL add-on sudah aktif
- Verifikasi kredensial database

### 502 Bad Gateway
- Aplikasi mungkin gagal start
- Cek logs untuk error message
- Pastikan Apache bisa bind ke port 80

## 🔗 Link Berguna

- [Railway Documentation](https://docs.railway.app)
- [Railway MySQL](https://docs.railway.app/stores/databases)
- [Docker PHP Official Image](https://hub.docker.com/_/php)

---

**© 2026 Klinik Laktasi IKMI**
