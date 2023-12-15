### Project Setup
1 - Clone the project:
```
git clone https://github.com/cybernerdie/jubilant-octo-bassoon.git
```

2 - Copy the .env.example file to .env and edit the parameters (DB connection, for example):
```
cp .env.example .env
```

3 - Update the database setup  in the .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

4 - Install composer dependencies

```
composer install
```
  
5 - Run migrations

```
php artisan migrate
```

6 - Run the project

```
php artisan serve
```

7 - Run test

```
php artisan test
```

8 - If you are on mac, the default value for pdf_to_text_bin_path in the pdf.php config should work, you can follow this guide to setup for windows https://github.com/spatie/pdf-to-text
