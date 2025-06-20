# Laravel Skill Test

This project is a skill test for Laravel 12, implementing RESTful APIs for a `Post` model with support for **drafts**, **scheduled publishing**, and **user-authenticated actions**.

---

## ✅ Features

- Authentication using Laravel’s built-in session and cookies
- Create, Read, Update, Delete (CRUD) for posts
- Save posts as **drafts**
- Schedule posts for future publishing using **Laravel scheduler**
- Automatically publish posts when `published_at` time arrives
- Authorization to ensure only the post's **author** can update or delete
- JSON responses only (no views required)
- Feature tests covering all routes and conditions

---

## ⚙️ Tech Stack

- Laravel 12
- PHP 8.3
- SQLite (for simplicity in local dev)
- Tailwind/Inertia (present, but not used in this task)
- `php artisan test --filter=PostFeatureTest` for testing
- `php artisan schedule:work` for background scheduling

---

## 🚀 Getting Started


### 1. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

> Use `DB_CONNECTION=sqlite` in `.env` and create the database file if needed:

```bash
touch database/database.sqlite
```

### 3. Run Migration and Seeder

```bash
php artisan migrate --seed
```

---

## 🧪 Running Tests

Run feature tests using:

```bash
php artisan test
```

Test coverage includes:
- Authenticated and unauthenticated access
- Validation of `draft` and `scheduled` posts
- Authorization on update and delete

---

## ⏰ Scheduled Publishing

To enable automatic publishing of scheduled posts, run:

```bash
php artisan schedule:work
```

This will automatically run the `posts:publish-scheduled` command every minute, publishing posts whose `published_at` has passed and `is_draft` is `true`.

---

## 👤 Author

**Farhan Akmal Shaleh**  
Skill Test Submission
