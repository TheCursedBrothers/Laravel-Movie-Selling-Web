# Laravel Movie Selling Website

A web application built with Laravel that allows users to browse and purchase movies online.

## Overview

This project is a comprehensive movie e-commerce platform featuring:
- User authentication and authorization
- Movie catalog browsing with search and filter capabilities
- Shopping cart functionality
- Secure checkout process
- User profiles and order history

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/Laravel-Movie-Selling-Web.git
   cd Laravel-Movie-Selling-Web
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Copy the environment file and configure it:
   ```
   cp .env.example .env
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Set up your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. Run the database migrations and seed data:
   ```
   php artisan migrate --seed
   ```

## Running the Application

1. Start the Laravel development server:
   ```
   php artisan serve
   ```

2. Compile assets and run Tailwind CSS:
   ```
   npm run dev
   ```

3. Access the application in your browser at `http://localhost:8000`

## Features

- **User Management**: Registration, login, profile management
- **Movie Catalog**: Browse movies by category, search, and view details
- **Shopping Cart**: Add movies to cart, update quantities, remove items
- **Checkout**: Secure payment processing
- **Order Management**: View order history and status

## Technologies Used

- **Backend**: Laravel, PHP
- **Frontend**: Blade templates, Tailwind CSS, JavaScript
- **Database**: MySQL
- **Authentication**: Laravel Breeze/Sanctum

## License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT).
