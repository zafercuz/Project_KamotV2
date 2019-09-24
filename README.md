# Project_KamotV2
To clone this Laravel app and get it working you'll need the prerequisites installed
* XAMPP with PHP
* Composer
* MSSQL driver installed to php (https://docs.microsoft.com/en-us/sql/connect/php/microsoft-php-driver-for-sql-server?view=sql-server-2017)

If all prerequisites are met then follow this steps
1. git clone the project to a directory
2. cd into project directory
3. git checkout laravel
4. composer install
5. cp .env.example .env
6. php artisan key:generate
7. In the .env file fill in the DB_HOST(sqlsrv), DB_PORT(1433), DB_DATABASE, DB_USERNAME, and DB_PASSWORD options to match the credentials of the database
