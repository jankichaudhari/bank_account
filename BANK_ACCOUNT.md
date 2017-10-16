## CLI application for Simulated bank account
This is CLI application built using PHP and MySQL that simulates a bank account and includes following functionality :
* Open/Create Account
* Apply Overdraft
* Deposit funds
* Withdraw funds
* Display Balance
* Close Account

This system only allows to run from command line at the moment. I t can be extended for web interface.
This system built using yii2 framework.

#### Requirements

* PHP 7.0 or greater
* MySQL 4.1.2 or greater

#### Installation

* After downloading and extracting from email attachment you will find folder, which contains this 
    "README.md", "bank_account" and "bank_account.sql"
* Import `bank_account.sql` in your mysql server, so `bank_account` app can access it.
* Place `bank_account` folder in your root directory or respective directory where you can run command line application" 
* Now open project directory `bank_account` and configure your database credential in `bank_account/config/db.php` 
    and `bank_account/config/test__db.php` (this will be used by unit test).
* Now you should be ready to use the application.

#### How to use

* Run `php yii help account` to see available commands which are following
- `php yii account/apply-overdraft <number>`  This command allows to apply overdraft to the user's bank account. The overdraft limit is already set.
- `php yii account/balance <number>` This command will display balance of the user's bank account
- `php yii account/close <number>` This command will close the user account.
- `php yii account/create <name>` This command will open new bank account for the user by taking username.
- `php yii account/deposit <number> <amount>` This command will deposit user's fund in respective bank account.
- `php yii account/withdraw <number> <amount>` This command allows user to withdraw fund from respective bank account.
* Run `php yii help account/<sub-command>` to see detailed help of each sub command.


#### Run unit test

* As mentioned above please configure your database credential in `bank_account/config/test__db.php`
* Run `vendor/bin/codecept run` to run all the unit tests
* Run ` vendor/bin/codecept run unit models/AccountTest:<test_suite_name>` to run specific test suite.

#### Structure

Here I used Yii2 framework. So there are so plenty of files which are not created by me. But they are useful if this project needs to be
extended.

Here I created following files :

* `bank_account/commands/AccountController.php` which contains all the main commands needs to be run from CLI.
* `bank_account/models/Account.php` which is model for database table `account` and related methods.
* `bank_account/tests/unit/models/AccountTest.php` which contains required unit tests for the model.