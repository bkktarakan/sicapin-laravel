# Panduan Deploy ke Hosting (sicapin.bkktarakan.id) via Git

Tujuan: menyamakan fitur aplikasi di hosting dengan kode lokal/GitHub, **tanpa mengubah data yang sudah ada di database/storage online**.

Status saat ini:
- Kode lokal sudah identik dengan `https://github.com/bkktarakan/sicapin-laravel.git` branch `main`.
- Hosting masih berisi file hasil upload manual (belum git). Panduan ini memindahkan hosting ke deployment berbasis git **tanpa menyentuh folder production yang sedang live** sampai semua siap.

Semua migrasi di `database/migrations/` sudah diperiksa: hanya `CREATE TABLE` / `ADD COLUMN`, tidak ada yang menghapus data. Laravel otomatis melewati migrasi yang sudah pernah dijalankan di database tersebut, jadi `php artisan migrate` aman dijalankan ulang.

---

## 1. Backup dulu (WAJIB, jangan skip)

- **Database**: cPanel → phpMyAdmin → pilih database SICAPIN → tab **Export** → Quick → Go. Simpan file `.sql` ke komputer.
- **File sertifikat upload**: cPanel File Manager → masuk ke folder `storage/app/public` di aplikasi yang sedang live → compress jadi `.zip` → download ke komputer.
- **File `.env` production**: buka di File Manager, copy semua isinya ke catatan lokal (notepad). Berisi `APP_KEY`, kredensial database, dan config mail asli — ini **harus dipakai lagi**, jangan sampai hilang atau tertimpa `.env.example`.

## 2. Clone repo ke folder BARU (bukan ke folder production yang sedang live)

cPanel → **Git™ Version Control** → Create:
- Clone URL Repository: `https://github.com/bkktarakan/sicapin-laravel.git`
- Repository Path: folder baru, misal `/home/USERNAME/sicapin-new` (jangan `public_html` atau folder aplikasi yang sedang live — supaya situs yang sedang berjalan tidak terganggu sama sekali selama proses ini)
- Branch: `main`

## 3. Siapkan folder baru lewat Terminal/SSH

```bash
cd ~/sicapin-new
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

## 4. Salin `.env` dan data lama ke folder baru

```bash
cp ~/path-folder-lama/.env ~/sicapin-new/.env
cp -r ~/path-folder-lama/storage/app/public ~/sicapin-new/storage/app/public
php artisan storage:link
```

Ganti `~/path-folder-lama` dengan path folder aplikasi yang sedang live di hosting. Langkah ini memastikan koneksi database, `APP_KEY`, dan file sertifikat yang sudah pernah di-upload tetap sama persis — tidak ada data yang hilang atau berubah.

## 5. Jalankan migrasi & cache

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`--force` aman karena hanya menambah tabel/kolom baru sesuai kondisi migrasi yang sudah diperiksa di atas; tidak ada `DROP`/`DELETE` data pada migrasi manapun.

## 6. Verifikasi sebelum go-live

Cek dulu folder baru berjalan benar sebelum dipublikasikan — misalnya via subdomain sementara yang document root-nya diarahkan ke `~/sicapin-new/public`, atau cek koneksi DB lewat `php artisan tinker` tanpa expose ke publik dulu.

## 7. Switch folder production (downtime singkat)

Setelah yakin folder baru oke:

```bash
mv ~/path-folder-lama ~/sicapin-laravel-backup-$(date +%Y%m%d)
mv ~/sicapin-new ~/path-folder-lama
```

Lalu pastikan **Document Root** domain di cPanel → Domains masih menunjuk ke path yang benar (folder `public` Laravel, biasanya `~/path-folder-lama/public`).

## 8. Untuk update berikutnya

Cukup buka cPanel → Git Version Control → repo ini → **Pull or Deploy**, lalu ulangi langkah 3 dan 5 (composer/npm/migrate) bila ada perubahan dependency atau schema baru.
