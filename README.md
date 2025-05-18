## Installation Commands

To get started, follow these commands:

```bash
#make sure to enable GD extension in your apache server (Xampp , Wamp ,laragon ..etc)

# Install dependencies
composer install

# Install Node.js dependencies
npm install

# Link storage
php artisan storage:link

# Migrate database 
php artisan migrate:fresh --seed

# Run the project
php artisan serve
npm run dev 


