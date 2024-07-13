Setup

Clone the repository:

git clone [<repository-url>](https://github.com/abdoroot/royalclass.git)
cd royalclass

Install dependencies:


    composer install


Update .env with your database credentials.

Generate application key:

        php artisan key:generate

Run migrations and seeders:

        php artisan migrate
        php artisan db:seed --class=UsersTableSeeder

Test Credentials

Use these credentials to test the API:

Admin User:
Email: admin@example.com
Password: 123456

Normal User 1:
Email: abdelhadi@example.com
Password: 123456

Normal User 2:
Email: user2@example.com
Password: 123456

Postman Collection
royal calss.postman_collection.json
Download the Postman collection for testing the API endpoints:

Postman Collection
Usage

Start the Laravel server:

        php artisan serve

Import the Postman collection and begin testing the endpoints.