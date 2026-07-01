# Panduan Penggunaan Fitur TravelGo (Step-by-Step)

Dokumen ini berisi panduan lengkap langkah demi langkah untuk mencoba dan menggunakan semua fitur aplikasi **TravelGo** dari sudut pandang **Customer (Pengguna)** dan **Admin (Pengelola)**.

---

## WEBSITE : https://pdpl-proyek-production.up.railway.app

## 1. Akun Akses Default (Kredensial)

Gunakan akun berikut untuk melakukan simulasi login:

### Akun Administrator:
* **Halaman Login**: `http://localhost:3000/login`
* **Email**: `admin@travelgo.com`
* **Password**: `admin123`

### Akun Customer:
* **Halaman Login**: `http://localhost:3000/login`
* **Email**: `budi@example.com`
* **Password**: `cust123`

---

## 2. Alur Penggunaan Fitur Customer (Pengguna)

### A. Registrasi dan Login Terpadu
1. Buka browser dan arahkan ke alamat `http://localhost:3000/`.
2. Klik tombol **Daftar** (Register) untuk membuat akun baru atau **Masuk** (Login) jika menggunakan akun default.
3. Pada form login terpadu (`/login`), masukkan email dan password, lalu klik **Masuk**. Sistem akan secara otomatis mengarahkan Anda ke Dashboard sesuai dengan peran (*role*) akun Anda (Customer ke `/customer`, Admin ke `/admin/dashboard`).

### B. Pencarian Jadwal dan Pemesanan Kursi (Multi-Passenger)
1. Di Dashboard Customer, gunakan form pencarian: pilih **Kota Asal**, **Kota Tujuan**, **Tanggal Keberangkatan**, dan **Jumlah Penumpang**, lalu klik **Cari**.
2. Pilih jadwal keberangkatan yang sesuai dari hasil pencarian, lalu klik **Pesan Tiket**.
3. Di Halaman Detail Pemesanan:
   - Pilih kursi yang kosong dari tata letak visual (*Seat Layout*). Jumlah kursi yang Anda pilih harus sesuai dengan jumlah penumpang yang dicari.
   - Setelah kursi dipilih, form input detail penumpang akan muncul secara dinamis di bawahnya.
   - Masukkan **Nama Lengkap**, **Nomor Identitas (NIK/Paspor)**, dan **Nomor Telepon** untuk **masing-masing kursi/penumpang**.
4. Klik **Kirim Pemesanan**. Kursi Anda akan otomatis dikunci (*held*) selama **10 menit** dengan status booking `pending`.

### C. Simulasi Pembayaran
1. Anda akan diarahkan ke halaman Simulasi Pembayaran.
2. Masukkan **Metode Pembayaran** (misal: Transfer Bank), **Nomor Rekening**, dan **PIN**.
3. Klik **Bayar Sekarang**.
   - Jika PIN benar, status booking berubah menjadi `confirmed`, pembayaran tercatat sebagai lunas, dan e-ticket otomatis diterbitkan.
   - Jika waktu 10 menit habis sebelum Anda membayar, pemesanan otomatis berstatus `expired` dan kursi dilepas.

### D. Melihat E-Ticket dan Kode QR
1. Pergi ke menu **Tiket Saya** atau **Riwayat Pemesanan** di pojok kanan atas.
2. Pada pemesanan berstatus `confirmed`, klik **Lihat E-Ticket**.
3. Halaman akan menampilkan rincian perjalanan, nama-nama penumpang untuk setiap kursi, dan kode QR unik yang merujuk pada kode tiket tersebut.

### E. Pembatalan Mandiri (Direct Cancellation)
1. Di menu **Riwayat Pemesanan**, pilih pemesanan berstatus `pending` (belum dibayar).
2. Klik **Batalkan Pesanan** di bagian detail pemesanan.
3. Status booking akan langsung berubah menjadi `cancelled` secara instan dan kursi langsung dilepas.

### F. Pengajuan Pembatalan (Cancellation Request - Butuh Approval)
1. Untuk pemesanan yang **sudah dibayar** (`confirmed`):
2. Buka detail pemesanan tersebut di dashboard Anda.
3. Klik tombol **Ajukan Pembatalan**.
4. Masukkan **Alasan Pembatalan**, lalu klik **Kirim Pengajuan**. Status pemesanan akan tetap `confirmed`, tetapi ada log permintaan pembatalan berstatus `pending` yang dikirim ke Admin.

### G. Pengajuan Reschedule (Ubah Jadwal - Butuh Approval)
1. Untuk pemesanan yang **sudah dibayar** (`confirmed`):
2. Buka detail pemesanan dan klik **Ajukan Reschedule**.
3. Pilih **Jadwal Baru** yang memiliki rute yang sama dari daftar yang tersedia.
4. Pilih nomor kursi baru pada jadwal tersebut, lalu masukkan **Alasan Reschedule**.
5. Klik **Kirim Pengajuan**. Status permintaan reschedule akan tercatat sebagai `pending` dan menunggu persetujuan admin.

---

## 3. Alur Penggunaan Fitur Admin (Pengelola)

### A. Dashboard Ringkasan & Monitoring
1. Login menggunakan akun Administrator.
2. Di halaman Dashboard Admin (`/admin/dashboard`), Anda dapat memantau total seluruh transaksi pemesanan, jumlah booking lunas, akumulasi pendapatan, dan jumlah total armada/jadwal yang beroperasi.

### B. Pemesanan Offline (Offline Booking)
1. Masuk ke menu **Pemesanan** dan klik **Tambah Pemesanan Offline**.
2. Pilih jadwal perjalanan dan masukkan nomor kursi yang dipilih.
3. Isi data penumpang secara lengkap (Nama, Identitas, Telepon) untuk setiap kursi.
4. Klik **Kirim Pemesanan**. Karena dilakukan oleh admin, status pemesanan offline ini langsung diset sebagai `confirmed` dan e-ticket langsung diterbitkan.

### C. Persetujuan Pembatalan (Approve/Reject Cancellation)
1. Masuk ke menu **Permintaan Pembatalan** (*Cancellation Requests*) di panel samping admin.
2. Di sini admin dapat melihat semua daftar pengajuan pembatalan dari customer.
3. Klik **Detail** pada salah satu permintaan:
   - Klik **Setujui**: Sistem akan memproses pembatalan dalam satu transaksi, mengubah status booking menjadi `cancelled`, melepas kursi terkait agar bisa dipesan kembali, dan mengirim notifikasi real-time ke customer.
   - Klik **Tolak**: Permintaan pembatalan ditolak, booking tetap berstatus `confirmed`, dan alasan penolakan dikirim ke customer.

### D. Persetujuan Reschedule (Approve/Reject Reschedule)
1. Masuk ke menu **Permintaan Reschedule** (*Reschedule Requests*) di panel samping admin.
2. Klik **Detail** untuk melihat jadwal asal, jadwal tujuan, serta kursi tujuan yang diajukan oleh customer.
3. Klik **Setujui**:
   - Sistem akan melakukan pengecekan ulang ketersediaan kursi secara *real-time* (menggunakan *row-level database locking* `FOR UPDATE`).
   - Jika kursi masih tersedia, sistem akan memindahkan kursi booking ke jadwal/kursi baru, membebaskan kursi lama, dan memperbarui e-ticket. Customer akan menerima notifikasi bahwa reschedule disetujui.
4. Klik **Tolak**: Permintaan reschedule ditolak, booking tetap pada jadwal semula, dan admin dapat menyertakan catatan penolakan.

### E. Monitoring Notifikasi Real-Time (Socket.IO)
1. Masuk ke menu **Notifikasi** (`/admin/notifications`) pada dashboard admin.
2. Anda dapat menulis pesan pada textarea **Kirim Notifikasi Baru** lalu menekan tombol kirim untuk melakukan *broadcast* ke seluruh pengguna yang sedang aktif secara real-time menggunakan Socket.IO.
3. Di sisi kanan atau bagian bawah, admin dapat melihat log riwayat seluruh aktivitas notifikasi sistem (notifikasi pemesanan baru, pembayaran lunas, pembatalan, reschedule, dll.) yang terus diperbarui secara dinamis tanpa perlu me-refresh halaman.
