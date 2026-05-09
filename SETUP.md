# TravelGo — Setup Guide

A booking management system for a bus/shuttle transportation company. This guide walks you through setting up the project on your local machine using **XAMPP** (MySQL/MariaDB) and optionally **MySQL Workbench**.

---

## Prerequisites

Make sure you have the following installed:

| Software | Version | Download |
|---|---|---|
| **XAMPP** | Latest (with PHP 8.2+) | https://www.apachefriends.org/ |
| **Composer** | Latest | https://getcomposer.org/ |
| **Node.js** | v18 or higher | https://nodejs.org/ |
| **Git** | Latest | https://git-scm.com/ |
| MySQL Workbench *(optional)* | Latest | https://dev.mysql.com/downloads/workbench/ |

---

## Step 1 — Clone the Repository

```bash
git clone <repository-url>
cd 2372004-2372007
```

---

## Step 2 — Install Dependencies

Install PHP and JavaScript dependencies:

```bash
composer install
npm install
```

---

## Step 3 — Configure Environment

Copy the example environment file and generate an application key:

```bash
copy .env.example .env
php artisan key:generate
```

Then open the `.env` file and update the **database settings** to match your XAMPP configuration:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travelgo
DB_USERNAME=root
DB_PASSWORD=
```

> **Note:** XAMPP's default MySQL user is `root` with an empty password. If you changed yours, update accordingly.

---

## Step 4 — Create the Database

You need to create an **empty** database named `travelgo` before running migrations.

### Option A: Using phpMyAdmin (XAMPP)

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Open your browser and go to `http://localhost/phpmyadmin`.
3. Click the **"New"** button on the left sidebar.
4. Enter `travelgo` as the database name.
5. Set the collation to `utf8mb4_general_ci`.
6. Click **"Create"**.

### Option B: Using MySQL Workbench

1. Open MySQL Workbench and connect to your local server (`127.0.0.1`, user `root`, no password).
2. In the query editor, run:
   ```sql
   CREATE DATABASE travelgo CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```
3. Click the refresh button on the **Schemas** panel to see the new database.

---

## Step 5 — Run Migrations & Seed the Database

This single command creates **all** tables and fills them with sample data:

```bash
php artisan migrate --seed
```

> **⚠️ Important:** This command should only be run on an **empty database** (first-time setup). If you already have data and want to start fresh, use `php artisan migrate:fresh --seed` instead (this drops all tables first).

You should see output like:

```
INFO  Running migrations.

  0001_01_01_000000_create_users_table .............. DONE
  0001_01_01_000001_create_cache_table .............. DONE
  0001_01_01_000002_create_jobs_table ............... DONE
  2024_01_01_000001_create_vehicles_table ........... DONE
  2024_01_01_000002_create_routes_table ............. DONE
  ...

INFO  Seeding database.
```

### Sample Accounts

The seeder creates the following accounts (password for all: `password`):

| Email | Role |
|---|---|
| `admin@travelgo.com` | Admin |
| `budi@example.com` | Customer |
| `siti@example.com` | Customer |

---

## Step 6 — Start the Application

Run the development server:

```bash
composer dev
```

This starts three services simultaneously:
- **Laravel server** at `http://localhost:8000`
- **Queue worker** for background jobs
- **Vite** for front-end asset compilation

Open your browser and go to **http://localhost:8000** 🎉

---

## Team Collaboration Workflow

When working with teammates, follow these rules to avoid database errors.

### After Pulling Changes (`git pull`)

After your teammate pushes new code (possibly with new migrations), do this:

```bash
git pull
composer install       # in case new PHP packages were added
npm install            # in case new JS packages were added
php artisan migrate    # runs ONLY the new migrations, keeps your data
```

> **Note:** Do NOT run `php artisan migrate --seed` or `php artisan db:seed` after pulling. Seeders are for first-time setup only — running them again will cause duplicate data errors.

### How to Change the Database Structure

**Never edit an existing migration file.** Your teammates have already run the old version and Laravel won't re-run it. Always create a **new** migration instead.

#### Example: Adding a `phone` column to the `users` table

```bash
php artisan make:migration add_phone_to_users_table
```

Then edit the newly created file:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone', 20)->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('phone');
    });
}
```

Commit and push. When your teammate pulls and runs `php artisan migrate`, Laravel will apply only this new change.

### If the Database Gets Messy

If you or your teammate runs into weird errors, the "nuclear option" resets everything:

```bash
php artisan migrate:fresh --seed
```

This drops **all** tables, recreates them from scratch, and re-inserts the sample data. All existing data will be lost.

### Quick Reference

| Situation | Command |
|---|---|
| First time setup (empty database) | `php artisan migrate --seed` |
| After `git pull` | `php artisan migrate` |
| Database is broken / need a clean reset | `php artisan migrate:fresh --seed` |
| You need to change a table | `php artisan make:migration <description>` |

---

## Troubleshooting

### "SQLSTATE[HY000] [1049] Unknown database 'travelgo'"
You haven't created the database yet. Go back to Step 4.

### "Table already exists" when running `php artisan migrate`
Your database already has tables. Use `php artisan migrate:fresh --seed` to reset.

### "Duplicate entry" when running `php artisan db:seed`
Seeders already ran before. Don't run seeders on a database that already has data. Use `php artisan migrate:fresh --seed` to reset.

### "composer dev" fails or Vite doesn't start
Make sure you ran `npm install` (Step 2). Also verify that Node.js is installed by running `node -v`.

### MySQL won't start in XAMPP
Port 3306 may be in use. Open XAMPP → Config → MySQL (`my.ini`) and change the port, or close any other MySQL instances.

---

## Summary of Commands

```bash
# 1. Clone and enter the project
git clone <repository-url>
cd 2372004-2372007

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
copy .env.example .env
php artisan key:generate

# 4. Create the "travelgo" database via phpMyAdmin or MySQL Workbench

# 5. Run migrations and seed sample data
php artisan migrate --seed

# 6. Start the app
composer dev
```
