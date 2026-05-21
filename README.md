# Digital Memo Archiving & Management System (DMAMS)

DMAMS is a premium, web-based internal administrative platform designed to digitally store, organize, retrieve, and manage official school memos and documents. It replaces physical filing systems to prevent document loss, improve accessibility, and streamline administrative efficiency.

---

## Key Features

- **Dual-Source Document Acquisition**:
  - **Direct Webcam Scanning**: Scan physical documents using a webcam stream. The pages are captured individually and compiled client-side into a single, clean A4 PDF file using `jsPDF`.
  - **Standard File Uploads**: Upload existing document formats including PDF, JPG, JPEG, and PNG (up to 10MB).
- **Auto-Generated Tracking Numbers**: Automatic generation of unique, structured memo tracking numbers (e.g., `REG/2026/001`) based on the originating department, the memo's year, and a sequential counter.
- **Advanced Search & Filtering**: Locate memos quickly using text searches (subject/description) or filters for department, category, and date ranges.
- **Role-Based Access Control (RBAC)**:
  - **Admin**: Full system access, dashboard metrics, audit trails, user management (create/delete accounts), and soft-delete recovery.
  - **Staff**: Can upload memos, edit metadata for memos they uploaded, and search/view files.
  - **Viewer**: Read-only access to browse, search, and download memos.
- **Audit Trails**: Secure logging of all user activities (uploads, downloads, updates, deletions, and restorations) to ensure administrative accountability.
- **Soft Deletion & Recovery**: Deleted records are soft-deleted and can be reviewed/restored by administrators to prevent accidental loss.

---

## Tech Stack

- **Backend**: Laravel 13 (PHP 8.4)
- **Frontend**: Blade Templates, Tailwind CSS (harmonious, dark-themed layout with glassmorphic elements and subtle micro-animations)
- **Database**: SQLite (configured for lightweight, local development)
- **Client-Side Compilation**: jsPDF for merging scanned images into PDFs

---

## Setup & Installation

Follow these steps to set up DMAMS locally:

### Prerequisites
- PHP 8.4+
- Composer
- SQLite3

### Steps

1. **Clone the repository and enter the directory**:
   ```bash
   cd MemoScan
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment File**:
   Copy `.env.example` to `.env`:
   ```bash
   copy .env.example .env
   ```

4. **Initialize Database**:
   Run database migrations and seed default administrative credentials and roles:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Start the Application**:
   You can start the local development server:
   ```bash
   php artisan serve
   ```
   Or access it via Laravel Herd/Valet at `http://memoscan.test`.

---



## Running Tests

DMAMS comes with a comprehensive feature test suite covering authentication, permissions, audit logs, file uploads, and webcam scanned document ingestion.

To run the tests:
```bash
php vendor/phpunit/phpunit/phpunit
```
Or:
```bash
php artisan test
```
