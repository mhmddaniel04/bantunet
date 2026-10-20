# BantuNet — Intelligence Volunteer Matching in Event Management System

Final Year Project (CSP600/CSP650), Bachelor of Computer Science (Hons.) Multimedia Computing,
College of Computing, Informatics and Mathematics, UiTM.

BantuNet helps small and medium non-profit organisations (NPOs) publish volunteer initiatives,
manage applications and generate engagement reports, while volunteers get **AI-powered
recommendations** of initiatives that match their skills and interests.

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3, Laravel |
| Frontend | Blade, Tailwind CSS v4, Vite |
| Database | PostgreSQL + pgvector |
| AI | Google Gemini Embedding API (`gemini-embedding-001`, 768 dimensions) |
| Matching | Hybrid score: cosine similarity (pgvector) + skill/category/availability rules |

## Local setup

Requirements: PHP 8.3 (pdo_pgsql, fileinfo, gd, intl), Composer 2, Node 20.19+/22.12+,
PostgreSQL 16+ with the `vector` extension.

```bash
git clone https://github.com/mhmddaniel04/bantunet.git
cd bantunet
composer install
npm install
cp .env.example .env        # then fill in DB_PASSWORD and GEMINI_API_KEY
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

In PostgreSQL, run `CREATE EXTENSION vector;` on the `bantunet` and `bantunet_test`
databases before migrating.

## Author

Muhamad Daniel bin Ahmad Asli — supervised by Prof. Madya Dr. Suzana binti Ahmad