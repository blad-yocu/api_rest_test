## configuración inicial, instalar paqueteria de php

composer install

## Copia el archivo .env.example a .env(el comando es dependiendo tu OS):

copy .env.example .env

## Genera la key de laravel con el siguiente comando

php artisan key:generate

## Coloca tus claves en el alchivo .env de Marvel Comics API information (https://developer.marvel.com):

## 1

API_KEY_PUBLIC=

## 2

API_KEY_PRIVATE=

## 3

TS=

## Rutas ejemplo para realizar peticiones:

## 1

1- localhost/marvel/colaborators/captain america --> regresa escritores y editores de los comics del personaje

## 2

2- localhost/marvel/characters/iron man -->regresa los personajes y comics con quien interactuo

## 3

3- localhost/marvel/colaborators/ ---> regresa lista de nombres de personajes
