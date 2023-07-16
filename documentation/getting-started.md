Follow the steps below for deployment.

# Step 1

Have installed **composer version 2.5.5**, validate **php version ^8.2.1**

# Step 2

For the database, **MySql** is used, check if it is installed.

# Step 3

Execute the following commands

- **composer install**
- **php artisan key:generate**, to execute this command verify that the **.env** file exists.
- **php artisan cache:clear**
- **php artisan config:clear**
- **php artisan config:cache**

For production effect, you must modify the value in the variable **APP_ENV** to **production**

# Step 4 

To begin interacting with the **database**, follow these steps.

- Create a database with the name **ship1place**
- Add in **DB_USERNAME** and **DB_PASSWORD**, the user name and password of the database.
- In case you have mysql service running on another port, please put the port in **DB_PORT**
- Execute the command **php artisan migrate --seed** to run migrations and seeders.
- If the database already exists and you just want to add new migrations, run **php artisan migrate**
- If the database already exists and you want to replace everything and run the seeders, run **php artisan migrate:reset** and then **php artisan migrate --seed**

# Step 5

To use the log jobs, you must follow these steps.

- Uncomment the line **#extension=zip** in **Php.ini**
- Execute locally **php artisan schedule:work**