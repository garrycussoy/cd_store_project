# CD Store API (By: Garry Ariel)
This API is designed for everyone who runs a CD rental. With this API you can handle the transaction easily. You can insert and update category and CD. You can also add new customer and save their information in database. You can also manage the transaction, where customer can rent more than 1 CD's at a time (but they need to return those CD together, cannot return it one by one).

## Technology Implementation
This API is built using Lumen framework (a micro-framework by Laravel) with PHP version 7.0 and using MySQL database version 5.6.

## Requirement
1. You need to install Lumen framework. Before installing Lumen, you will need to install some of the following.
- PHP (version 7.0 or greater)
- OpenSSL PHP extension
- PDO PHP extension
- Mbstring PHP extension
- Composer
2. MySQL database of version 5.6 or greater. To connect to database, go to .env file, and change the following line with your database information.
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD

## How to Run the Application
Follow below instructions to run the application.
1. Open the terminal
2. Go to cd_store_project folder
3. Run the following command: php -S localhost:8000 -t public

## API Blueprint
For more information about how to use the API's, you can read the detail at api_blueprint.xlsx file.

## Contact
For more information, critic, or advice, please contact me at garryarielcussoy@gmail.com, thank you.