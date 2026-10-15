# Airport Operations & Flight Scheduling System

An academic PBL project: a Laravel monolith for managing airport operations and flight
scheduling. This repository currently covers **Stage 1** (database foundation) and
**Stage 2** (authentication, authorization, admin dashboard and Module 1 master-data CRUD).

## Tech stack

- Laravel 12 (Blade + Tailwind CSS + Alpine.js)
- MariaDB / MySQL (production-style), SQLite (in-memory) for the test suite
- Vite for asset bundling
- PHPUnit for automated tests
- Laravel Pint for code style

## Requirements

- PHP 8.2+ with the `pdo_mysql` extension
- Composer 2
- Node.js 18+ and npm
- A running MariaDB / MySQL server

## Installation

```bash
composer install
npm install
```

Create the environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env` (see below), then create the schema and demo data:

```bash
php artisan migrate --seed
```

Build the front-end assets:

```bash
npm run build
```

Start the development server:

```bash
php artisan serve
```

### Database configuration

The application uses a single database. Example `.env` values:

```
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=airport_operations_db
DB_USERNAME=root
DB_PASSWORD=
```

`.env` is git-ignored and must never be committed.

## Authentication & roles

Authentication uses Laravel Breeze (Blade stack). **Public registration is disabled** —
administrative accounts are provisioned explicitly with the artisan command below.

The `users` table carries a `role` column (`admin`, `manager`, `agent`). Admin routes are
protected by the `auth` middleware plus a custom `role:admin` middleware
(`App\Http\Middleware\EnsureUserHasRole`), with policies as a second layer of defence:

- Unauthenticated users are redirected to the login screen.
- Authenticated non-admins receive **HTTP 403** for admin routes.

### Creating or resetting an administrator

No production credentials are hard-coded. Create or reset an admin interactively:

```bash
php artisan app:create-admin
```

Or supply options directly:

```bash
php artisan app:create-admin admin@example.com --name="Site Admin"
```

(If `--password` is omitted the command prompts for it securely.)

### Seeded demo accounts

`php artisan migrate --seed` creates sample logins. These are demo values only — replace
them before any real use:

| Email                | Password   | Role    |
| -------------------- | ---------- | ------- |
| admin@airport.test   | password   | admin   |
| manager@airport.test | password   | manager |
| agent@airport.test   | password   | agent   |

## Admin area (Module 1)

All administration lives under `/admin` and is available to `admin` users only.

- **Dashboard** – live record counts (airports, airlines, aircraft, flights, schedules,
  terminals, gates, runways) and the most recent flight schedules with status badges.
- **Airlines** – CRUD, search by name/codes/country, active filter.
- **Airports** – CRUD, search, active filter, coordinates and elevation validation.
- **Aircraft** – CRUD, airline filter, status filter, capacity consistency validation.
- **Terminals** – CRUD, per-airport code uniqueness, airport filter.
- **Gates** – CRUD, per-terminal code uniqueness, airport/terminal/status filters.
- **Runways** – CRUD, per-airport designation uniqueness, airport/status filters.

Behaviour notes:

- Records are paginated (10 per page) with the query string preserved.
- Unique codes (airline IATA/ICAO, airport IATA/ICAO, aircraft registration, terminal/
  gate/runway codes) are validated at the request layer and protected by DB constraints.
- Parents with dependants cannot be deleted; attempts are blocked with a friendly message
  and any residual foreign-key error is caught and reported.
- Multi-statement writes run inside database transactions.

## Testing

The test suite runs on an in-memory SQLite database (`phpunit.xml`):

```bash
php artisan test
```

Coverage includes authentication, role/authorization rules, dashboard statistics, CRUD for
every Module 1 resource, duplicate-code validation, invalid foreign keys, deletion guards
and view rendering.

## Project structure (key paths)

```
app/Console/Commands/CreateAdminUser.php   Admin provisioning command
app/Http/Controllers/Admin/                Module 1 resource controllers + dashboard
app/Http/Middleware/EnsureUserHasRole.php  Role middleware (alias: role)
app/Http/Requests/Admin/                   Form request validation
app/Policies/                              Authorization policies per resource
app/Services/                              Aircraft / Gate assignment domain services
database/migrations/                       Schema (20 domain tables + 7 framework tables)
database/seeders/                          Demo data
resources/views/layouts/admin.blade.php    Admin layout (sidebar)
resources/views/admin/                     Dashboard + Module 1 views
routes/web.php                             Admin route group
tests/Feature/Admin/                       Stage 2 feature tests
```

## Manual verification checklist

1. `php artisan migrate:fresh --seed` completes without errors.
2. `php artisan app:create-admin` creates a working admin login.
3. Visiting `/admin` while logged out redirects to `/login`.
4. Logging in as an `agent`/`manager` and opening `/admin` returns **403**.
5. Logging in as an admin shows the dashboard with correct counts.
6. Create/edit/delete works for each of Airlines, Airports, Aircraft, Terminals, Gates
   and Runways, including search and filters.
7. Creating a record with a duplicate code shows a validation error.
8. Deleting an airline that has aircraft, or a terminal that has gates, is blocked with a
   message.
9. `/register` returns **404** (registration disabled).
10. `php artisan test` is green.

## Roadmap

Later stages add flight scheduling, gate/aircraft assignment UI, passengers, bookings,
check-in, baggage, delayed/cancelled flight operations and reporting dashboards.
