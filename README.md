# 📦 QR Code Link Generator API (Laravel, PHP, PostgreSQL)

A professional, containerized application for generating, managing, and tracking branded QR code links. Built with **Laravel 11**, **Tailwind CSS**, **PostgreSQL**, and **Redis**, following a modern **Service-Query-Repository** architecture.

## 📑 Summary

This project is a high-performance QR code link management service designed for scalability, security, and a premium user experience.

- **Architecture:** Decoupled business logic using **Service-Query-Repository** patterns.
- **Frontend:** Modern, responsive dashboard built with **Tailwind CSS** and **Blade**.
- **Performance:** Redis-backed caching for QR codes and async job processing for scan tracking.
- **Analytics:** Integrated **GeoIP** tracking for real-time visitor insights (Country, City, Device, OS).
- **Security:** Laravel Sanctum for secure authentication and stateful session management.
- **Reliability:** Comprehensive PHPUnit suite with 100% coverage for core business logic.

---

## 🌟 Key Features

- **Shorten URLs:** Generate unique QR codes and short links for any valid URL.
- **Custom Aliases:** Users can provide a specific alias (e.g., `my-brand`) for their short links.
- **Link Expiration:** Set expiration dates for links; expired links return `410 Gone`.
- **Advanced QR Config:** Customize foreground/background colors and resolution (up to 1000px) directly via the dashboard or API.
- **Security:** Integrated password protection for links with a modern dark-mode UI, and a secure Partner API gate.
- **Partner Program:** Integrated "Partner" status with a toggle in account settings to enable/disable advanced API access.
- **Account Management:** Premium dark-mode profile page for managing personal identity, security credentials, and partner status.
- **QR Branding:** Customize QR code color, background, size, and logo.
- **Analytics:** Track scans, devices, OS, and country-level location (via GeoIP) for detailed usage statistics.
- **Caching Layer:** Redis-backed queue for async jobs and optimized QR code generation with intelligent caching.
- **Advanced Architecture:**
  - **Repositories:** Centralized data persistence for core entities.
  - **Queries:** Separated data retrieval and aggregation logic for performance and clarity.
- **Data Integrity:** Soft deletes on links to maintain historical analytics even after removal.
- **Rate Limiting:** Protects against abuse and spam by limiting link creation per user.
- **Interactive Dashboard:** Blade UI for managing links, branding, and analytics.
- **Testing Suite:** PHPUnit feature and unit tests.

---

## 🚀 Getting Started

### Prerequisites
- **Docker** & **Docker Compose**

### 1. Automatic Setup (Recommended)
Run the setup script to build containers (App, Database, Redis, and Queue Worker), fix permissions, install dependencies, run migrations/seeders, and run tests:

```bash
chmod +x setup.sh
./setup.sh
```
- The app will be available at http://localhost:8001
- **Redis** is used for caching and queueing.
- **PostgreSQL 15** is the primary database.

### 2. Manual Setup (Alternative)
If you prefer to run the commands manually:
```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan cache:clear
docker compose exec app php artisan test
docker compose exec -d queue php artisan queue:work
```

---

## 📖 API Documentation

The dynamic API documentation (Swagger UI) is available at:

👉 [http://localhost:8001/api/documentation](http://localhost:8001/api/documentation)

It allows you to explore and test all available endpoints directly from your browser.

---

## 🏗️ Deployment

The project includes deployment scripts for automated CI/CD pipelines (e.g., GitHub Actions):

- **Staging:** `scripts/deploy-staging.sh`
- **Production:** `scripts/deploy-production.sh`

These scripts require `DEPLOY_WEBHOOK_URL` and `DEPLOY_TOKEN` environment variables to be set in your CI environment.

---

## 📖 API Endpoint Reference

### 🔗 Create Short Link
**POST** `/api/links`

**Request Body:**
```json
{
  "original_url": "https://www.example.com/very/long/url",
  "custom_alias": "optional-custom-alias",
  "expires_at": "2026-12-31T23:59:59+00:00",
  "password": "optional-password",
  "color": "#000000",
  "background_color": "#ffffff",
  "size": 300
}
```

**Response (201 Created):**
```json
{
  "short_code": "AbCd12",
  "short_url": "http://localhost:8001/AbCd12",
  "qr_code_download_url": "http://localhost:8001/api/links/AbCd12/download-qr"
}
```

**Response (422 Unprocessable Entity):**
```json
{
  "message": "The given data was invalid.",
  "errors": { "original_url": ["The original url must be a valid URL."] }
}
```

### 🔀 Redirect
**GET** `/{shortCode}`
- **Success:** `302 Found` (Redirects to destination)
- **Password Protected:** Prompts for password if set
- **Not Found:** `404 Not Found`
- **Expired:** `410 Gone`

### 🎨 Update QR Branding
**PUT** `/api/links/{link}/qr-branding`
- Update color, background, size, or logo for an existing QR code.

### 📊 Analytics
**GET** `/api/analytics/{shortCode}`
- Returns scan/device analytics for a link.

### 🔑 Authentication & External Integration

This application is designed for both human users via the dashboard and **external developers** via the API. External systems can bypass the dashboard and automate link management using **Personal Access Tokens**.

1. **Generate a Token**: Log in to the dashboard and go to the **API Access** section to generate a new key.
2. **Authorize Requests**: Include your token in the `Authorization` header of all API requests:
   ```bash
   Authorization: Bearer YOUR_API_TOKEN
   ```

**Core Auth Endpoints:**
- `POST /api/register` — Register a new user
- `POST /api/login` — Login and receive API token
- `POST /api/logout` — Logout (token revoke)
- `GET /api/me` — Get current user info
- `GET /api/tokens` — List personal access tokens
- `POST /api/tokens` — Create new personal access token (with optional description)
- `DELETE /api/tokens/{tokenId}` — Revoke access

---

## 🗄️ Database Structure (Eloquent Models)

**Link**
| Column         | Type         | Description                       |
|----------------|--------------|-----------------------------------|
| id             | INT          | Primary Key, Auto-increment       |
| user_id        | INT          | Foreign key to users              |
| original_url   | TEXT         | The original long URL             |
| short_code     | VARCHAR(50)  | Unique, random or custom alias    |
| password       | VARCHAR(255) | (Optional) Hashed password        |
| expires_at     | DATETIME     | (Optional) Expiration datetime    |
| created_at     | DATETIME     | Timestamp of creation             |

**QrCode**
| Column           | Type         | Description                       |
|------------------|--------------|-----------------------------------|
| id               | INT          | Primary Key, Auto-increment       |
| link_id          | INT          | Foreign key to links              |
| color            | VARCHAR(7)   | Hex color code                    |
| background_color | VARCHAR(7)   | Hex color code                    |
| size             | INT          | Size in pixels                    |
| logo_path        | VARCHAR(255) | (Optional) Logo file path         |

---

## 🚀 CI/CD & Deployment

- **GitHub Actions** for CI/CD: runs tests, builds Docker images, and triggers deploys.
- **Docker Compose**: Orchestrates app, queue, db, and redis.
- **Secrets**: Configure `DEPLOY_TOKEN`, `DEPLOY_WEBHOOK_URL_STAGING`, and `DEPLOY_WEBHOOK_URL` in your repository for deployment.

---

## 🛠 Tech Stack

- **Laravel 11**: Modern PHP framework
- **PHP 8.4**: Latest PHP features
- **PostgreSQL**: Robust relational database
- **Redis**: Queue backend and caching
- **Docker Compose**: Orchestration
- **SimpleSoftwareIO/simple-qrcode**: QR code generation

---

## 🔨 Development Commands

**Run command inside container**
```bash
docker compose exec app <command>
```

**Clear Cache**
```bash
docker compose exec app php artisan cache:clear
```

**Run Migrations**
```bash
docker compose exec app php artisan migrate
```

**Run Tests**
```bash
docker compose exec app php artisan test
```

**Start Queue Worker**
```bash
docker compose exec queue php artisan queue:work
```

**Check Container Status**
```bash
docker compose ps
```

**View Logs**
```bash
docker compose logs -f app
```

---

## 🧪 Testing

The project uses **PHPUnit** for automated testing.

**Run all tests**
```bash
docker compose exec app php artisan test
```

---

## 📁 Project Structure

```
.
├── app/
│   ├── Http/Controllers/Api/LinkController.php   # Main API logic
│   ├── Http/Requests/                           # Validation for all endpoints
│   ├── Services/                                # Business logic for links/QR
│   ├── Models/                                  # Eloquent models
├── resources/views/                             # Blade templates
├── routes/api.php                               # API route definitions
├── tests/Feature/                               # Feature tests
├── tests/Unit/                                  # Unit tests
├── docker-compose.yml                           # Docker setup
├── QUEUE_WORKER_PRODUCTION.md                   # Queue worker instructions
└── README.md                                    # Project documentation
```

---

## 📃 License

MIT License. See LICENSE file for details.
