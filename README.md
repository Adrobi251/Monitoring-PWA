# Konsultasi Layanan Mahasiswa-Dosen (Laravel 13 PWA)

## Setup

1. **Install dependencies**:
   ```powershell
   composer install
   ```

2. **Generate application key**:
   ```powershell
   php artisan key:generate
   ```

3. **Run migrations**:
   ```powershell
   php artisan migrate
   ```

4. **Install Node.js dependencies and build assets**:
   ```powershell
   npm install
   npm run build
   ```

5. **Start development server**:
   ```powershell
   php artisan serve
   ```

## Roles & Demo Accounts

- **Dosen (Lecturer)**:
  - Email: `dosen@test.com`
  - Password: `password`
  - NIDN: `D001`
  - ID: 3

- **Mahasiswa (Student)**:
  - Email: `mahasiswa@test.com`
  - Password: `password`
  - NIM: `123456789`
  - ID: 4
  - Dosen Wali ID: 3 (assigned to dosen@test.com)

## Routes

- **Mahasiswa Routes** (`/mahasiswa/*`):
  - Dashboard: `/mahasiswa/dashboard`
  - Schedule: `/mahasiswa/dashboard/jadwal`
  - History: `/mahasiswa/dashboard/riwayat`
  - Submit Consultation: `/mahasiswa/pengajuan` (POST)

- **Dosen Routes** (`/dosen/*`):
  - Dashboard: `/dosen/dashboard`
  - Schedule: `/dosen/dashboard/jadwal`
  - History: `/dosen/dashboard/riwayat`
  - Approve Consultation: `/pengajuan/{pengajuan}/status` (PUT)
  - Assign Mahasiswa: `/dosen/mahasiswa/assign` (POST, by NIM)

## PWA Installation

1. Open the application in your browser
2. Click the install button in the browser toolbar (Chrome/Edge)
3. The app will be added to your home screen and work offline

## Features

- Role-based authentication (mahasiswa/dosen)
- Schedule management with overlap validation
- Consultation submission and approval workflow
- Transaction protection with `lockForUpdate` for double booking prevention
- PWA support with manifest and service worker
- Offline fallback
- Database notifications
- Comprehensive testing with PHPUnit

## Testing

Run the consultation flow tests:
```powershell
php artisan test --filter ConsultationFlowTest
```