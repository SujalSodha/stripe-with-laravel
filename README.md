# Laravel Stripe Payment Integration Project

## Description

This Laravel project demonstrates a comprehensive integration with Stripe for handling payments. It provides a robust foundation for any e-commerce platform, or any application requiring payment processing capabilities.

## Features

- **Stripe Integration**: Seamlessly integrated Stripe for handling payments, ensuring secure and efficient transaction processing.
- **Customer Management**: Efficiently manage customer information, including storing payment methods and billing details.
- **One-Time Payments**: Process one-time payments securely and swiftly.
- **User Authentication**: Secure user authentication and registration system using Laravel’s built-in authentication features.
- **Responsive Design**: Mobile-friendly and responsive design for an optimal user experience across all devices.
- **Extensive Documentation**: Detailed documentation to help developers understand the integration process and customize the application as needed.

## Installation

To set up this project on your local machine, follow these steps:

1. **Clone the Repository**
   ```sh 
   git clone https://github.com/SujalSodha/stripe-with-laravel.git
   cd laravel-stripe-project
   ```
   
2. **Install Dependencies**
    ```sh composer install
    npm install
    ```
   
3. **Environment Configuration**
   ```sh
   cp .env.example .env
   php artisan key:generate
   ```
   
4. **Database Migration**  
   ```sh
   php artisan migrate
   ```

5. **Run the Application**  
   ```sh
   php artisan serve
   npm run dev
   ```
