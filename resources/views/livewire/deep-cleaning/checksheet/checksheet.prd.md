# Product Requirements Document (PRD): TPM Checksheet

## 1. Latar Belakang & Tujuan
**TPM (Total Productive Maintenance) Checksheet** adalah fitur yang digunakan oleh tim maintenance untuk memantau dan mencatat kondisi fisik atau performa spesifik dari mesin-mesin pabrik (khususnya tipe *Machining Center*). 

Pencatatan ini dilakukan secara rutin setiap bulan dalam satu tahun berjalan. Tujuannya adalah untuk mendeteksi degradasi performa mesin sejak dini sebelum terjadi kerusakan fatal (*breakdown*). Apabila hasil pengukuran (*actual*) mendekati atau melewati batas toleransi (*standard limit*), tim maintenance dapat segera mengambil tindakan perbaikan.

## 2. Parameter yang Diukur
Ada 3 jenis inspeksi (Type Checksheet) utama yang dipantau dalam fitur ini:

1. **GATA-GATA (Goyang/Kelenturan Spindle)**
   - **Fungsi:** Mengukur tingkat keausan atau goyangan pada *spindle* mesin.
   - **Satuan:** milimeter (mm).
   - **Batas Toleransi (Standard):** Maksimal 2 mm.

2. **CLAMP ARBOR (Daya Cengkram Spindle)**
   - **Fungsi:** Mengukur seberapa kuat *spindle* mencengkram *tool* (pisau potong).
   - **Satuan:** Kilo Newton (kN).
   - **Batas Toleransi (Lower Limit):** 
     - Untuk mesin tipe **BT 30** (mesin kecil): Minimal 2 kN.
     - Untuk mesin tipe **BT 40** (mesin besar): Minimal 5 kN.

3. **RUN OUT (Penyimpangan Putaran Spindle)**
   - **Fungsi:** Mengukur penyimpangan aksis saat *spindle* berputar, yang akan berpengaruh langsung pada tingkat presisi produk yang diproses mesin.
   - **Satuan:** Mikron (μ).
   - Terdiri dari 2 pengukuran sekaligus:
     - **Kelurusan:** Maksimal 10 μ.
     - **Putaran:** Maksimal 5 μ.

## 3. Alur Penggunaan (User Flow)

1. **Membuat Template (Generate)**
   - Karena pencatatan dilakukan tiap bulan, Admin/Manager akan menekan tombol **Generate**.
   - Admin memilih Mesin, Tahun, dan Jenis Checksheet (bisa lebih dari satu).
   - Sistem akan membuat 12 baris data kosong (Januari - Desember) di database untuk mesin tersebut agar siap diisi.

2. **Memfilter Data (Pencarian)**
   - Tim di lapangan membuka halaman Checksheet.
   - Mereka memilih dropdown **Type**, lalu **Year**, dan terakhir **Machine**.
   - *Dropdown Machine* hanya akan menampilkan mesin-mesin yang sudah di-*generate* datanya pada tahun dan tipe tersebut.

3. **Input / Edit Data Bulanan**
   - Setelah filter dipilih, pengguna menekan tombol **Input Data**.
   - Sebuah modal (jendela popup) akan muncul. Pengguna memilih bulan (misalnya 'Januari').
   - Sistem secara cerdas akan memunculkan *form input* yang sesuai dengan Tipe Checksheet yang dipilih.
     - *Jika Gata-Gata:* Muncul form input "Gata mm".
     - *Jika Clamp Arbor:* Muncul form input "Clamp kN".
     - *Jika Run Out:* Muncul 2 form input "Kelurusan" dan "Putaran".
   - Pengguna juga dapat memasukkan nama pemeriksa (PIC) dan catatan (Remark) jika ada kerusakan atau *abnormality*.

4. **Visualisasi Data (Grafik / Chart)**
   - Setelah data diisi, halaman akan menampilkan **Grafik Garis (Line Chart)** berisi 12 titik (bulan 1-12).
   - Garis tebal menampilkan **Nilai Aktual** dari mesin.
   - Garis putus-putus menampilkan **Batas Standar (Limit)**.
   - Jika garis aktual melewati garis batas standar, pengguna secara visual akan langsung tahu bahwa mesin tersebut butuh perbaikan (*overhaul*).

## 4. Keamanan & Hak Akses
- Siapapun yang bisa login dapat melakukan **Input Data**.
- Namun, **Edit Data** (mengubah data bulan sebelumnya yang mungkin salah ketik) dan **Generate Data** dibatasi hanya untuk:
  - User dengan role `Admin` atau `Manager`.
  - User spesifik dengan nomor JID tertentu (berdasarkan referensi lama: `JID01497` atau `JID02589`).

---
**Kesimpulan:** Fitur ini bertindak sebagai rapor kesehatan bulanan bagi *spindle* mesin pabrik. Dengan melihat grafik, manajemen bisa memprediksi kapan sebuah mesin harus dijadwalkan turun mesin (*maintenance* berat) sebelum mesin tersebut benar-benar mati mendadak saat produksi.
