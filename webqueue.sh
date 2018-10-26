#!/bin/bash

php artisan schedule:run -n -q 2>/dev/null >/dev/null &
#> /dev/null 2>&1

echo 'OK'
