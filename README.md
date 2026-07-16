<div align="center">
  <img src="https://github.com/abiliodacosta/sistema_geografia_identifikasaun_edifisio_PNTL/blob/main/vizitor/img/pntlall.png" alt="PNTL Logo" width="100"/>

  # PNTL Infrastructure & Management System

  **A modern Geographic Information System (GIS) and Management Dashboard for Polísia Nasionál Timor-Leste.**
</div>

<hr>

## 📖 Deskripsi Proyek (Project Description)

Aplikasi Web ini dibangun untuk **Polísia Nasionál Timor-Leste (PNTL)** guna memetakan, mengelola, dan memantau infrastruktur serta fasilitas kepolisian di seluruh wilayah. Dilengkapi dengan peta interaktif, asisten *chatbot* cerdas, dan *dashboard* admin yang komprehensif, sistem ini mempermudah pencarian informasi dan manajemen data secara *real-time*.

## ✨ Fitur Utama (Key Features)

*   🗺️ **Peta Interaktif (Interactive Maps):** Visualisasi lokasi gedung PNTL, pos polisi, dan infrastruktur lainnya lengkap dengan detail lokasi dan rute.
*   🤖 **Asisten Chatbot Pintar:** Terintegrasi langsung untuk membantu masyarakat mendapatkan informasi seputar fasilitas PNTL dengan cepat.
*   📊 **Dashboard Admin Modern:** Panel kontrol aman untuk mengelola data gedung, munisipio (kabupaten), posko, dan kategori fasilitas.
*   📈 **Statistik & Analitik Visual:** Menyajikan data statistik administratif dalam bentuk grafik batang dan diagram lingkaran yang responsif.
*   📱 **Desain Responsif:** Tampilan modern dan profesional yang menyesuaikan dengan sempurna di berbagai perangkat (Desktop, Tablet, dan Mobile).
*   ✉️ **Sistem Pesan & Notifikasi:** Integrasi SMTP Email untuk komunikasi internal.

## 💻 Tampilan Aplikasi (Screenshots)

*(Ganti URL gambar di bawah ini dengan gambar aplikasi Anda yang sudah di-upload ke folder `docs/` atau image hosting)*

### Peta Interaktif & Chatbot
![Peta & Chatbot](https://via.placeholder.com/800x400.png?text=Screenshot+Peta+dan+Chatbot)

### Dashboard Admin
 <img src="https://github.com/abiliodacosta/sistema_geografia_identifikasaun_edifisio_PNTL/blob/main/Screenshot%202026-07-16%20133518.png" alt="PNTL Logo" width="1000px" heigh="100"/>


## 🚀 Teknologi yang Digunakan (Tech Stack)

*   **Frontend:** HTML5, CSS3 (Bootstrap/Tailwind), JavaScript
*   **Backend:** PHP / Node.js *(Sesuaikan dengan framework yang Anda gunakan, misal Laravel)*
*   **Database:** MySQL / PostgreSQL
*   **Lainnya:** Leaflet.js / Google Maps API (untuk peta), Chart.js (untuk grafik)

## 🛠️ Cara Instalasi (Installation Setup)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di mesin lokal Anda:

1. **Clone Repository ini:**
   ```bash
   git clone https://github.com/username-anda/pntl-app.git
   cd pntl-app
   ```

2. **Install Dependensi:**
   ```bash
   # Jika menggunakan Composer (PHP/Laravel)
   composer install

   # Jika menggunakan NPM (Node.js)
   npm install
   ```

3. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database Anda.
   ```bash
   cp .env.example .env
   ```

4. **Migrasi Database:**
   ```bash
   php artisan migrate
   ```

5. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://localhost:8000` atau `http://localhost/pntl-app/`

## 👥 Kontributor (Contributors)

*   **[Nama Anda]** - *Fullstack Developer* - [@username-anda](https://github.com/username-anda)

## 📄 Lisensi (License)

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---
<div align="center">
  Dibuat dengan ❤️ untuk Timor-Leste.
</div>
