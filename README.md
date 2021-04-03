## About Laravel Dashboard

Laravel Dashboard is a custom developed package by SpiderWorks, that extends capability of Laravel framework to further level. 

## How to use


### Step 1
First install Laravel framework version 8 by following command
```sh
composer create-project laravel/laravel app-name 8
```
### Step 2
Install Laravel Dashboard package by following command
```sh
composer require spiderwork/laravel-dashboard
```
### Step 3
Enable login authentication
```sh
php artisan breeze:install
```
### Step 4
Run these command
```sh
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spiderworks\Webadmin\WebadminServiceProvider"
```
### Step 5
Create a database and connect it to <b>.env</b> file. the run the following command
```sh
php artisan migrate
```


### Step 7
In Providers/RouteServiceProvider.php Change line number 20
```sh
public const HOME = 'dashboard';
``` 
to
```sh
public const HOME = 'admin/dashboard';
```

### Finally
Make Middileware and secure login using Spatie role management system