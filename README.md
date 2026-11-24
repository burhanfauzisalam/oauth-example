# OAuth App

Aplikasi web berbasis **Laravel 13** yang menyediakan autentikasi **hanya melalui akun Google** (OAuth 2.0). Cocok dijadikan fondasi SaaS: setiap pengguna login dengan akun Google pribadinya, tanpa perlu email/password.

## Fitur

- 🔐 **Login & signup via Google** — satu tombol, tanpa form pendaftaran.
- 👤 **Pembuatan user otomatis** — akun Google baru langsung dibuatkan user; akun yang sudah terdaftar cukup diambil datanya.
- 🖼️ **Avatar disimpan lokal** — avatar Google diunduh ke `storage/app/public/avatars` saat login, sehingga tidak terpengaruh rate limit CDN Google (`lh3.googleusercontent.com`).
- 🖥️ **Halaman dashboard** — menampilkan data akun (nama, email, avatar, tanggal bergabung).
- 🛡️ **Route terproteksi** — `/dashboard` hanya dapat diakses setelah login.
- 🚪 **Logout** — menghapus sesi dengan aman.
- 🧪 **Test otomatis** — 10 feature test mencakup alur login, callback, dan proteksi route.

## Teknologi

- [Laravel 13](https://laravel.com) (PHP 8.5)
- [Laravel Socialite](https://laravel.com/docs/socialite) v5
- Tailwind CSS v4
- MySQL (default) / SQLite

---

## Prasyarat

- PHP ≥ 8.5
- Composer 2
- Node.js & npm (untuk build aset frontend)
- Salah satu database: MySQL atau SQLite

---

## Instalasi

### 1. Install dependency

```bash
composer install
npm install
```

### 2. Konfigurasi environment

Salin `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Setting koneksi database di `.env` (contoh MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oauth
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan migration

```bash
php artisan migrate
```

Migration akan membuat tabel `users` (dengan kolom `google_id` dan `avatar`) serta tabel pendukung sesi/cache.

### 4. Buat symlink storage

Agar file yang diunggah (termasuk avatar Google) dapat diakses publik:

```bash
php artisan storage:link
```

---

## 🔑 Membuat Client ID & Client Secret Google

Ikuti langkah berikut untuk mendapatkan kredensial OAuth Google.

### Langkah 1 — Buat project di Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Klik dropdown **project** (di pojok kiri atas) → **New Project**.
3. Beri nama project (misal `oauth-app`) → klik **Create**.
4. Pastikan project baru terpilih pada dropdown.

### Langkah 2 — Set OAuth consent screen

1. Dari menu (☰) pilih **APIs & Services → OAuth consent screen**.
2. Pilih tipe **External** → **Create**.
3. Isi:
   - **App name**: nama aplikasi Anda (misal `OAuth App`).
   - **User support email**: alamat email Anda.
   - **Developer contact information**: email Anda.
4. Klik **Save and Continue** (biarkan scope default) sampai selesai.
5. *(Opsional untuk produksi)* Setelah aplikasi siap, klik **Publish App**. Selama berstatus **Testing**, hanya email yang didaftarkan sebagai **Test users** yang bisa login.

### Langkah 3 — Buat OAuth Client ID

1. Dari menu pilih **APIs & Services → Credentials**.
2. Klik **+ Create Credentials → OAuth client ID**.
3. Pada **Application type**, pilih **Web application**.
4. Pada **Authorized redirect URIs**, tambahkan URL callback aplikasi Anda:

   ```
   http://localhost:8000/auth/google/callback
   ```

   > ⚠️ Pastikan URL ini **persis sama** dengan `GOOGLE_REDIRECT_URI` di `.env`. Jika berbeda, akan muncul error `redirect_uri_mismatch`.

5. Klik **Create**. Akan muncul modal berisi:
   - **Client ID**
   - **Client Secret**

> 💡 Jika tombol **OAuth client ID** berwarna abu-abu, kemungkinan Langkah 2 (consent screen) belum diselesaikan.

### Langkah 4 — Isi `.env`

Salin nilai di atas ke `.env`:

```env
GOOGLE_CLIENT_ID=xxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

> 🔒 **Jangan pernah** melakukan commit Client Secret ke repository publik. File `.env` sebaiknya tidak masuk ke version control (sudah tercantum di `.gitignore`).

### Langkah 5 — Bersihkan cache konfigurasi

```bash
php artisan config:clear
```

---

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka **http://localhost:8000** — Anda akan diarahkan ke halaman login. Klik **Lanjutkan dengan Google** untuk menguji.

### Build aset frontend

Saat pengembangan, Anda bisa menjalankan Vite dev server agar perubahan CSS/JS termuat otomatis:

```bash
npm run dev
```

Untuk produksi:

```bash
npm run build
```

---

## Rute Utama

| Metode | URI | Nama | Deskripsi |
|--------|-----|------|-----------|
| GET | `/` | — | Redirect ke `/dashboard` jika sudah login, selain itu ke `/login` |
| GET | `/login` | `login` | Halaman login (tombol Google) |
| GET | `/auth/google/redirect` | `auth.google.redirect` | Alihkan user ke layar consent Google |
| GET | `/auth/google/callback` | `auth.google.callback` | Terima callback dari Google, buat/ambil user, lalu login |
| GET | `/dashboard` | `dashboard` | Dashboard (membutuhkan autentikasi) |
| POST | `/logout` | `logout` | Logout |

---

## Struktur Kode

```
app/Http/Controllers/GoogleController.php   # Logika redirect & callback OAuth
app/Models/User.php                         # Model user (+ google_id, avatar)
config/services.php                         # Konfigurasi layanan (google)
database/migrations/...add_google_id...     # Tambah kolom google_id & avatar
resources/views/login.blade.php             # Halaman login
resources/views/dashboard.blade.php         # Halaman dashboard
routes/web.php                              # Definisi rute
tests/Feature/GoogleAuthTest.php            # Feature test autentikasi
```

---

## Testing

```bash
php artisan test
```

Suite mencakup:

- Guest diarahkan ke halaman login.
- Redirect ke Google menyertakan parameter `prompt=select_account` (selalu memunculkan layar pilih akun).
- Callback membuat user baru & login.
- Callback memakai user yang sudah ada (tanpa menimpa data).
- Guest/authenticated diarahkan dari `/` dengan benar.
- Penanganan pembatalan consent & sesi kedaluwarsa.
- Dashboard hanya bisa diakses user terautentikasi.

---

## Lisensi

Project ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
