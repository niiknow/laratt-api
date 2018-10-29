# laratt-api (Laravel Table Tenancy API)
> A rest-ful API supporting multi-tenant dynamic table manipulations 

This is an API built on top of the [laratt](https://github.com/niiknow/laratt) library.

Use-case/useful:
* Multitenancy is usually related to some kind of a Software as a Service (SaaS) app.
* Headless CMS
* Headless Profile management
* Headless Ecommerce or anything else
* Stashing of logs
* Email subscribers list
* Email bounce/blacklist
* Products API

This API give you the ability to quickly import hundred-of-thousands of rows.  Deploy this somewhere, use X-API-Key for private access, and let this API handle your database access.  Think of this as a Serverless/Headless kind of database endpoint.  Let this library worry about handling of your data.  Otherwise, just use the [laratt](https://github.com/niiknow/laratt) library in your own app.  This library give you a starter/bootstrap framework on how to make that possible.

# Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [API](#api)
5. [Query Syntax](#query-syntax)
6. [Future Feature TODO](#todo)
7. [Q and A](#Q-and-A)
8. [Docker](#docker)

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

**Configuration Note**
- `API_KEY`=set this to secure your api with `X-API-Key` header
- `AWS_BUCKET_AUDIT`=set all of the AWS configuration to enable s3 storage

## API
Expect two headers:
- `X-API-Key` the API_KEY above
- `X-Tenant` the tenant id - must start with alpha character with remaining character of alphanumeric.  Mininum of 3 characters and max of 20.

Separating Tenant and Table Name allow for better control and validation.  It also allow for future support of JWT/Token Auth that contain information about the Tenant.

**API Schema**
![](https://raw.githubusercontent.com/niiknow/laratt-api/master/api.png?raw=true)
Special multi-tables endpoint @ `/api/v1/tables/{table}`; where `{table}` is the table name you want to create.  `{table}` must be all lower cased alphanumeric with mininum of 3 characters to 30 max.  Example, let say `x-tenant: clienta` and `{table} = product`, then the resulting table will be `clienta_product`.

Also note that there are two ids: `id` and `uid`. `id` is internal to **laratt**.  You should be using `uid` for all operations.  `uid` is an auto-generated guid, if none is provide during `insert`.

Providing a `uid` allow the API `update` to effectively act as an `merge/upsert` operation.  This mean that, if you call update with a `uid`, it will `update` if the record is found, otherwise `insert` a new record.

- `/list` endpoint is use for query and bulk delete, see: [Query Syntax](#query-syntax)
- `/data` endpoint is use for returning jQuery DataTables format using [laravel-datatables](https://github.com/yajra/laravel-datatables).
- `/import` bulk import is csv to allow for bigger import.  Up to 10000 records instead of some small number like 100 for Azure Table Storage (also see admin config to adjust).  This allow for efficiency of smaller file and quicker file transfer/upload.
- `/truncate` truncate all data from table.
- `/drop` drop a table.  Why not?  Now you can do all kind of crazy stuff with table.

Also see [/api/documentation](http://laratt.test/api/documentation) for swagger docs.

What about your own/custom schema?  See example of our [Profile Schema](https://github.com/niiknow/laratt/blob/master/src/Models/ProfileModel.php#L78)

## Query-Syntax
This library provide simple query endpoint for search and bulk delete: `api/v1/profiles/list` or `api/v1/tables/{table}/list` - see **CRUD Format** above.

### Limiting

To limit the number of returned resources to a specific amount:

```
/list?limit=10
/list?limit=20
```

### Sorting

To sort the resources by a column in ascending or descending order:

```
/list?sort[]=column:asc
/list?sort[]=column:desc
```

You could also have multiple sort queries:

```
/list?sort[]=column1:asc&sort[]=column2:desc
```

### Filtering

The basic format to filter the resources:

```
/list?filter[]=column:operator:value
```

**Note:** The `value`s are `rawurldecode()`d.

#### Filtering Options

| Operator | Description | Example |
| --- | --- | --- |
| eq | Equal to | `/list?filter[]=column1:eq:123` |
| neq | Not equal to | `/list?filter[]=column1:neq:123` |
| gt | Greater than | `/list?filter[]=column1:gt:123` |
| gte | Greater than or equal to | `/list?filter[]=column1:gte:123` |
| lt | Less than | `/list?filter[]=column1:lt:123` |
| lte | Less than or equal to | `/list?filter[]=column1:lte:123` |
| ct | Contains text | `/list?filter[]=column1:ct:some%20text` |
| nct | Does not contains text | `/list?filter[]=column1:nct:some%20text` |
| sw | Starts with text | `/list?filter[]=column1:sw:some%20text` |
| nsw | Does not start with text | `/list?filter[]=column1:nsw:some%20text` |
| ew | Ends with text | `/list?filter[]=column1:ew:some%20text` |
| new | Does not end with text | `/list?filter[]=column1:new:some%20text` |
| bt | Between two values | `/list?filter[]=column1:bt:123\|321` |
| nbt | Not between two values | `/list?filter[]=column1:nbt:123\|321` |
| in | In array | `/list?filter[]=column1:in:123\|321\|231` |
| nin | Not in array | `/list?filter[]=column1:nin:123\|321\|231` |
| nl | Is null | `/list?filter[]=column1:nl` |
| nnl | Is not null | `/list?filter[]=column1:nnl` |

You can also do `OR` and `AND` clauses. For `OR` clauses, use commas inside the same `filter[]` query:

```
/list?filter[]=column1:operator:value1,column2:operator:value2
```

For `AND` clauses, use another `filter[]` query.

```
/list?filter[]=column1:operator:value1&filter[]=column2:operator:value2
```

## Features
- [x] multitenancy with `x-tenant` header
- [x] dynamic table as `tenantid_tablename`
- [x] simple and flexible CRUD (create, retrieve, update, delete) REST api
- [x] simple query and bulk delete `/list` REST endpoint
- [x] jQuery DataTables as `/data` endpoint with [laravel-datatables](https://github.com/yajra/laravel-datatables)
- [x] simplify installation with [rachidlaasri/laravel-installer](https://github.com/rashidlaasri/LaravelInstaller)
- [x] simplify backup with [spatie/laravel-backup](https://github.com/spatie/laravel-backup)   
- [x] simple authentication with `X-API-Key` header
- [x] pre-defined structured schema for `ProfileModel`
- [x] ecommerce and schedulable schema type for `TableModel` table
- [x] cloud auditable/s3 backed of individual record transaction.  This allow you to trigger lambda on some event instead of having to create scheduled jobs.
- [x] being able to include and exclude table from auditable - so you don't have to audit things like when you're using it for logging/caching or when client doesn't need it for some particular reason. 

## TODO
- [ ] refactor and test in separate library

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
