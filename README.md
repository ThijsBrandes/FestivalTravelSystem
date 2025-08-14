# Festival Travel System

A web application for booking festival travel tickets. Users can browse festivals, book tickets, and manage their travel plans all in one place.

## Features

- Browse and search for festivals
- View festival details including location, date, and ticket availability
- Book festival travel tickets
- Integrated travel arrangements with bus transportation
- Rewards system for loyal customers
- User authentication and profile management

## Prerequisites

- PHP 8.4 or higher
- Composer
- Node.js and NPM
- MySQL or another database supported by Laravel

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/ThijsBrandes/FestivalTravelSystem.git
   cd FestivalTravelSystem
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Create a copy of the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=festival_travel_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

8. Link storage:
   ```bash
   php artisan storage:link
   ```

## Running the Application

### Development Mode

You can run all necessary services with these two commands:

```bash
npm run dev
```
```bash
php artisan serve --port=8000
```

This will start:


1. Start the Laravel development server:

2. Compile frontend assets:

### Production Mode

1. Compile assets for production:
   ```bash
   npm run build
   ```

2. Configure your web server (Apache/Nginx) to point to the `public` directory

## Testing
Configure your testing environment by creating a `.env.testing` file based on your `.env` file, but change the database 
to a testing database.
and set the `APP_ENV` to `testing`.

Migrate the testing database:

```bash
php artisan migrate --env=testing
```

Then, run the tests using:

```bash
composer run test
```

## Additional Commands

### Database Management

- Fresh migration with seed data:
  ```bash
  php artisan migrate:fresh --seed
  ```

- Clear cache:
  ```bash
  php artisan cache:clear
  ```

### User Management

- Create a new admin user:
  ```bash
  php artisan make:user
  ```
