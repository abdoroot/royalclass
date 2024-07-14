# Laravel API Project

## Installation

1. Install Composer dependencies:
    ```sh
    composer install
    ```

2. Run the migrations:
    ```sh
    php artisan migrate
    ```

3. Seed the database with initial users:
    ```sh
    php artisan db:seed --class=UsersTableSeeder
    ```

## User Credentials

You can use the following credentials to log in:

- **Normal User 1**:
  - Email: abdelhadi@example.com
  - Password: 123456

- **Normal User 2**:
  - Email: user2@example.com
  - Password: 123456

- **Admin User**:
  - Email: admin@example.com
  - Password: 123456

## Postman Collection

The Postman collection is attached to this project. You can use it to test the API endpoints.

1. Import the collection into Postman.
2. Use the provided credentials to log in and test the API.

## Running Tests

To run the tests, use the following command:

        ```sh
        php artisan test --verbose
        ```

## API Endpoints
# Authentication
Login: POST /api/login

# Admin Routes (requires auth:sanctum and admin middleware)
List Users: GET /api/admin/users
Create User: POST /api/admin/users
Get User: GET /api/admin/users/{id}
Update User: PUT /api/admin/users/{id}
Delete User: DELETE /api/admin/users/{id}
Post List: POST /api/admin/post-list

# User Routes (requires auth:sanctum and check.notadmin middleware)
List Posts: GET /api/posts
Create Post: POST /api/posts
Get Post: GET /api/posts/{id}
Update Post: PUT /api/posts/{id}
Delete Post: DELETE /api/posts/{id}
Report Post: POST /api/report
User Details
Get Authenticated User: GET /api/user

## Notes
- Ensure you have the correct environment settings in your .env file, including database configuration.
- The Sessions middleware is enabled for the tests to work properly with authentication.

