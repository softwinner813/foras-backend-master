/************************************************
 * @Auth : SoftWinner813
 * @Date : 2020.3
 * @Desc : Foras Backend for website and mobile app
 ************************************************/

# Foras backend for website and mobile app

This project was built with Laravel6.0.
Default DB driver: Mysql

## Available Scripts

In the project directory, you can run:

### `composer install`

Confirm the existance of `vendor` directory

### `php artisan migrate` or `php artisan migrate:fresh`

Before run the above the command, you create the database. db name will be `foras` on local.

### `php artisan serve`

Runs the app in the development mode.<br />
Server will run on [http://127.0.0.1:8000](http://127.0.0.1:8000).

### `php artisan config:clear`, `php artisan cache:clear`

If you face some issues, you can clean cache and try again.

### More

Website front-end built with React.js 
When you are going to integrate the front-end code, you will add the contents of build directory to the public diretory of Backend project.
Note: except the `index.html`
And then, you will replace the `index.html` to `/resources/views/index.html`.

Done...

### ***** please check the project.txt file in the root directory *****