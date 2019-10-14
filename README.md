# Project_KamotV2
To clone this Laravel app and get it working you'll need the prerequisites installed
* XAMPP/Nginx with PHP
* Composer
* MSSQL driver installed to php (https://docs.microsoft.com/en-us/sql/connect/php/microsoft-php-driver-for-sql-server?view=sql-server-2017)

If all prerequisites are met then follow this steps
1. git clone the project to a directory
2. cd into project directory

(SHOULD BE CD'D INSIDE DIRECTORY UP UNTIL THE LAST PART)

3. git checkout laravel
4. composer install
5. cp .env.example .env
6. php artisan key:generate
7. In the `.env` file fill in the `DB_HOST=sqlsrv`, `DB_PORT=1433`, `DB_DATABASE=`(leave blank), DB_USERNAME, DB_PASSWORD, and `DB_DATABASE_BRANCH=zbranch_office` options to match the credentials of the database

(IF USING UBUNTU, AND SHOULD USE UBUNTU)

8. sudo chown -R my-user:www-data /path/to/your/laravel/root/directory
9. sudo find /path/to/your/laravel/root/directory -type f -exec chmod 664 {} \;
10. sudo find /path/to/your/laravel/root/directory -type d -exec chmod 775 {} \;
11. sudo chgrp -R www-data storage bootstrap/cache
12. sudo chmod -R ug+rwx storage bootstrap/cache
13. git config core.filemode false
