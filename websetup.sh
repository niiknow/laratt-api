#!/bin/bash

if [ ! -f .env ]; then
  echo '[i] copy env file'
  cp .env.example .env || true

  if [ -f .env ]; then
    echo '[i] generate encryption key'
    php artisan key:generate

    echo '[i] optimize production'
    composer app:production
    composer install --no-dev --optimize-autoloader
  fi
fi

#> /dev/null 2>&1

echo 'OK'
