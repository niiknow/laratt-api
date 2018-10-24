# tapi
> Table API

To create a private API supporting multi-tenant dynamic table manipulations.

Use-case/useful with SaaS (similar to Azure Table Storage):
* A need of profile endpoint.
* A need to provide some kind of a product endpoint.
* Being able to quickly load hundred-of-thousands of items.
* Ability to segment client/tenant data for security and scalability.
* Can even provide some kind of a endpoint to stash logs.

# Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [API](#api)
5. [Query Syntax](#query-syntax)
6. [Future TODO](#future-todo)
7. [Q and A](#Q-and-A)

## Requirements
- PHP 7.1+
- Composer
- Node JS
- NPM
- Laravel Valet (NGINX) or Homestead

## Installation
1. `git clone https://github.com/niiknow/tapi`
2. `cd tapi`
3. `composer install`
4. `npm install`
5. set your `.env` * set your admin credential and database login, etc...
6. `php artisan migrate:fresh --seed`
7. Run/Serve the Site
    - laravel valet: valet link tapi
    - homestead: homestead up
    [tapi.test/](tapi.test)

**Configuration Note**
- `ADMIN_TOKEN`=set this to secure your api with `x-token` header
- `AWS_BUCKET_AUDITABLE`=set all of the AWS configuration to enable s3 storage

## API
Expect two headers:
- `x-token` the ADMIN_TOKEN above
- `x-tenant` the tenant id - must be alphabetic, all lower case, less than 21 characters.

**CRUD Format**

| method | endpoint | name |
| --- | --- | --- |      
| GET | api/v1/profiles/list | api.profiles.list |
| POST,PUT,PATCH | api/v1/profiles/create | api.profiles.create |
| GET | api/v1/profiles/{uid}/retrieve| api.profiles.retrieve |
| POST,PUT,PATCH | api/v1/profiles/{uid}/update | api.profiles.update |       
| POST,DELETE | api/v1/profiles/{uid}/delete | api.profiles.delete |

Special multi-tables endpoint @ `/api/v1/tables/{table}`; where `{table}` is the table name you want to create.  

Also note that there are two ids: `id` and `uid`. `id` is internal to **tapi**.  You should be using `uid` for all operations.  `uid` is automatically generated guid if none is provide during `insert`.

Example, let say you `x-tenant: clienta` and `{table} = product`, then the resulting table will be `clienta_product`.

## Query-Syntax
Query endpoints: `api/v1/profiles/list` or `api/v1/tables/{table}/list`

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

## Future TODO
- [ ] Some Benchmarking
- [ ] Being able to exclude object from auditable to save cost?
- [ ] Bulk import.
- [ ] Merge (insert/update) feature.
- [ ] Query based delete.
- [ ] Other ideas?

## Q and A
> Why Laravel, and why not Lumin?

Laravel because we Eloquent provide everything we need.  Ability to set table prefix to support multitenancy.
Since it will be accessing the database, Lumin would not have made a lot of improvement, ref: https://medium.com/@laurencei/lumen-vs-laravel-performance-in-2018-1a9346428c01

> Why PHP vs (nodejs/lua/golang/csharp)?

Again, see why Laravel explained above.  PHP is easy to deploy.  We can simply deploy this on some cpanel and/or docker.  Hey, we can even do serverless (https://read.acloud.guru/serverless-php-630bb3e950f5) and combine with Aurora Serverless Database for high scalability.

# MIT
