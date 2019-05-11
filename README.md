# laratt-api (Laravel Table Tenancy API)
> A rest-ful API supporting multi-tenant dynamic table manipulations 

This is an API built on top of the [laratt](https://github.com/niiknow/laratt) library. Demo: https://laratt.niiknow.org/api/documentation X-API-Key: demo123

Use-case/useful:
* Multitenancy is usually related to some kind of a Software as a Service (SaaS) app.
* Headless CMS
* Headless Profile management
* Headless Ecommerce or anything else
* Stashing of logs
* Email subscribers list
* Email bounce/blacklist
* Products API

This API give you the ability to quickly import hundred-of-thousands of rows.  Deploy this somewhere, use `X-API-Key` header for private access, and let this API handle your database access.  Think of this as a Serverless/Headless kind of database endpoint.  Let this library worry about handling of your data.  Otherwise, just use the [laratt](https://github.com/niiknow/laratt) library in your own app.  This library is a demonstration of the laratt library.

# Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [API](#api)
5. [Futures](#features)
6. [Q and A](#Q-and-A)
7. [Docker](#docker)

## Requirements
- PHP 7.1+
- Composer
- Node JS
- NPM
- Laravel Valet (NGINX) or Homestead
- PHP [xdebug](http://www.artemdwo.com/install-php72-and-xdebug-on-mac-os-x) for phpunit code coverage

## Installation
1. `git clone https://github.com/niiknow/laratt-api`
2. `cd laratt-api`
3. `composer install`
4. `npm install`
5. set your `.env` by copying from `.env.example`
6. `php artisan key:generate` and `php artisan migrate:fresh --seed`
7. Run/Serve the Site
    - laravel valet: `valet link laratt`
    - homestead: `homestead up`
8. after `valet link laratt`, visit [laratt.test/](laratt.test) or npm run watch

**Production Deployment**
1. Package the project
```
composer app:package
```
2. Upload the resulting file in `storage/build/dist.tar.gz` to your server and unpack/extract.
3. Create the necessary database on your server and take note of the database credentials.
4. Visit `your-api.example.com/init.php` to initialize the project.  This will update required permissions for `storage/framework/`, `storage/logs/`, and `bootstrap/cache/` and create the `.env` file from `.env.example` file.  If it doesn't automatically redirect you to `/install`, then visit `/install` and complete the setup to finalize your `.env` file with the necessary database and other configuration.  Take note of the `API_KEY` that was generated or provide your own key to be use with `X-API-Key` header.
5. Congratulation, you're all set!

**Configuration/env Note**
- `API_KEY`=set this to secure your api with `X-API-Key` header
- `AUDIT_BUCKET`=set all of the AWS configuration to enable s3 storage
- `AUDIT_DISK`=set the aws disk

## API
Expect two headers:
- `X-API-Key` the API_KEY above
- `X-Tenant` the tenant id - must start with alpha character with remaining character of alphanumeric and underscore.  Mininum of 3 characters and max of 20.

Separating Tenant and Table Name allow for better control and validation.  It also allow for future support of JWT/Token Auth that contain information about the Tenant.

Run and visit `/api/documentation` for Swagger docs.

## Features
- [x] See list of features here [laratt](https://github.com/niiknow/laratt#features)
- [x] Simplify installation with [rachidlaasri/laravel-installer](https://github.com/rashidlaasri/LaravelInstaller).  
- [x] Simple authentication with `X-API-Key` header

## Q and A
> Why Laravel, and why not Lumin?

Laravel because Eloquent has everything we need.  Primarily, the ability to set table prefix for multitenancy support.  Because of all the feature of Laravel, this library was mostly completed within 3 days from start.  Also,
since our app will be accessing the database, Lumin would not have made a lot of performance improvement, ref: https://medium.com/@laurencei/lumen-vs-laravel-performance-in-2018-1a9346428c01 or https://jason.pureconcepts.net/2017/02/lumen-is-dead-long-live-lumen/

> Why PHP vs (nodejs/lua/golang/csharp)?

Again, see why Laravel explained above.  PHP is also easy to deploy, similar to other langs mentioned above.  We can simply deploy this on some cPanel or similar hosting and/or docker.  Hey, we can even do serverless (https://read.acloud.guru/serverless-php-630bb3e950f5) and combine with Aurora Serverless Database for high scalability.

## Docker
What we're doing with docker is to use the most common/Official Docker Images for demonstration.  For things like Letsencrypt SSL and custom docker container is outside the scope of this project.  This allow us to present a docker-compose file in clear and simple manner, so to help user understand and maybe even provide a starting template for more complex scenario.

See docker-compose.example.yml for demonstration.  Simply rename to docker-compose.yml and run:
```
docker-compose up
```

Note, we also configured `innodb_file_per_table=1` in `my.cnf` which may help improve performance in this particular usage.  It may degrade performance when the number of tables reach in the tens of thousands depending on your server resource.

# MIT
