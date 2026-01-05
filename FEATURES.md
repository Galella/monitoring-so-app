# Dokumentasi Fitur Aplikasi Monitoring SO

Dokumen ini menjelaskan secara rinci fitur-fitur yang tersedia dalam Aplikasi Monitoring SO.

## 1. Dashboard Utama

Halaman dashboard memberikan gambaran cepat mengenai kesehatan data operasional dan keuangan.

-   **Status Cards**: Menampilkan jumlah match, missing coin, dan missing CM secara real-time.
-   **Volume Chart (Stacked Bar)**:
    -   Menampilkan volume kontainer berdasarkan **Kereta** per bulan (6 bulan terakhir).
    -   Grafik bersifat "Grouped" (dikelompokkan), memudahkan perbandingan performa antar kereta dalam satu bulan.
    -   **Filter Otomatis**: Untuk user Admin Area, grafik ini hanya menampilkan data yang relevan dengan stasiun asal mereka.

## 2. Modul Monitoring & Rekonsiliasi

Fitur inti aplikasi untuk mencocokkan data Operasional (CM) dengan Data Keuangan (Coin/SO).

### Tab Data

1.  **All Data**: Menampilkan seluruh data yang masuk ke sistem.
2.  **Matched**: Menampilkan data yang **valid**, dimana data CM (Container/PPCW) cocok dengan data Coin (Nomor Order/SO).
3.  **Missing Coin (Unmatched CM)**:
    -   Menampilkan data operasional (CM) yang sudah ada, namun **belum memiliki** data order/keuangan pasangannya.
    -   Berguna untuk tim operasional mengejar tim sales/admin untuk input SO.
4.  **Missing CM (Unmatched Coin)**:
    -   Menampilkan data order (Coin) yang sudah diinput, namun **belum ada** realisasi operasionalnya (CM).
    -   **Fitur Spesial Admin Area**: User admin area akan otomatis melihat data yang `Stasiun Asal`-nya sesuai dengan area mereka, meskipun data tersebut belum ter-mapping secara sistem (area_id null).

### Fitur Pencarian & Ekspor

-   **Pencarian**: Dapat mencari berdasarkan Nomor Kontainer atau Nomor CM di seluruh status.
-   **Export Excel**: Mengunduh laporan monitoring ke dalam format Excel (`.xlsx`) sesuai dengan filter status dan pencarian yang sedang aktif.

## 3. Manajemen User & Role (RBAC)

Aplikasi menggunakan sistem hak akses bertingkat:

-   **Super Admin**: Memiliki akses penuh ke seluruh menu, seluruh data wilayah, dan seluruh area.
-   **Admin Wilayah**: Terbatas pada data di wilayah tertentu (misal: Daop 1, Daop 2).
-   **Admin Area**:
    -   Terbatas pada stasiun/area spesifik.
    -   Dashboard dan tabel monitoring otomatis terfilter hanya menampilkan data wilayah kerja mereka.

## 4. Audit Trail (Log Aktivitas)

Setiap perubahan data penting (Create, Update, Delete) dicatat sistem untuk keperluan audit, mencakup siapa yang mengubah, kapan, dan data apa yang berubah.
