##configuración inicial, instalar paqueteria de php

composer install

## Copia el archivo .env.example a .env(el comando es dependiendo tu OS):

copy .env.example .env

## general la key de laravel con el siguiente comando

php artisan generate:key

## coloca tus claves en el alchivo .env de Marvel Comics API information (https://developer.marvel.com):

API_KEY_PUBLIC=
API_KEY_PRIVATE=
TS=

##rutas ejemplo para realizar peticiones:

1- localhost/marvel/colaborators/captain america --> regresa escritores y editores de los comics del personaje
2- localhost/marvel/characters/iron man -->regresa los personajes y comics con quien interactuo
3- localhost/marvel/colaborators/ ---> regresa lista de nombres de personajes
