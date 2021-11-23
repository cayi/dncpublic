Proyecto Publico para elaborar un cuestionario para las necesidades
de capacitaci+on del Personal, utiliza el Framework de Laravel 8

Se intenta utilizar las mejores practicas como son:

 Priebas unitarias (en desarrollo)
 Controles flacos (Skinny controllers)
 Uso de Repositorios
 Uso de Modelos
 Importacion y Exportacion de Datos con hojas de Excel
 

pra subir a la nube
git add .
git commit -m "modif_20210507"
git push

Video de YOUTUBE del CRUD Laravel 8, ejemplo de EMpleados con Foto que sube a storage public/uploads
https://www.youtube.com/watch?v=9DU7WLZeam8

para ver rutas actvas
php artisan route:list

HTML on line editor and designer
https://html5-editor.net/

Le da un migrate:reset (drop all tables)
y crea las migraciones (php artisan migrate)
php artisan migrate:fresh

Genera los registros de Usuario Administrador y uno de prueba
php artisan db:seed


## running in heroki
MIRL760820HSLRMS01

[Click Here](https://radiant-earth-84938.herokuapp.com)

## heroku CLI to go the bash

`$heroku git:remote -a evaluacion-de-personal

`heroku run bash
php artisan migrate:refresh  
php artisan db:seed 
