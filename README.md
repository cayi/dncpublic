
SPANISH (see english notes below)

Proyecto Publico para elaborar un cuestionario para las necesidades
de capacitaci+on del Personal, utiliza el Framework de Laravel 8

Se intenta utilizar las mejores practicas como son:

 Priebas unitarias (en desarrollo)
 Controles flacos (Skinny controllers)
 Uso de Repositorios
 Uso de Modelos
 Importacion y Exportacion de Datos con hojas de Excel

 CARACTERISTICAS:
 Usa Laravel 8.0
 Importa / Exporta archivos de Excel modernos XLSX y antiguos XLS
 Soporta borrado logico de registros SOFTDELETES
 Usa livewire para importar archivos grandes de Excel, informa al usuario el avance.

 

 NOTAS:
 Es un proyecto EN DESARROLLO, algunas opciones aun no funcionan
 para ingresar usuario admin@gmail.com contraseña admin
 NO usar giones bajos en los nombres de las tablas
 USAR la letra s al final de los nombres de las tablas (probar)
 NO usar mayusculas entre medio de los nombres de Repositorios o Modelos
 Al IMPORTAR de Excel todas las celdas deben tener datos, o sea NO DEJAR CELDAS VACIAS
 EL formato DNC se puede cargar vacio, con las primeras 11 columnas o lleno con las 23 columnas
 La cantidad de registros que pude cargar a la vez en DNC son 1000 registros, no se si es por el tamaño
   del archivo o cantidad de registros, el error es: 
 En heroku solo soporta archivos viejos de Excel XLS 

pra subir a la nube
git add .
git commit -m "modif_20210507"
git push

Video de YOUTUBE del CRUD Laravel 8, ejemplo de EMpleados con Foto que sube a storage public/uploads
https://www.youtube.com/watch?v=9DU7WLZeam8

para ver rutas actvas
php artisan route:list

Le da un migrate:reset (drop all tables)
y crea las migraciones (php artisan migrate)
php artisan migrate:fresh

Genera los registros de Usuario Administrador y uno de prueba
php artisan db:seed


## running in heroki

[Click Here](https://radiant-earth-84938.herokuapp.com)

## heroku CLI to go the bash

`$heroku git:remote -a dnc

`heroku run bash
php artisan migrate:refresh  
php artisan db:seed 


ENGLISH
Public Project to develop a questionnaire for the needs Staff training, uses the Laravel 8 Framework
We try to use the best practices such as:

 Unit tests (under development)
 Skinny controllers
 Use of Repositories
 Use of Models
 Import and Export of Data with Excel sheets
 

 NOTES:

 DO NOT use underscores in table names
 USE the letter s at the end of table names 
 DO NOT use capital letters between the names of Repositories or Models
 
to go up to the cloud
git add.
git commit -m "modif_20210507"
git push

YOUTUBE video of CRUD Laravel 8, example of Employees with Photo that uploads to storage public / uploads
https://www.youtube.com/watch?v=9DU7WLZeam8

to see active routes
php artisan route: list

It gives you a migrate: reset (drop all tables)
and create the migrations (php artisan migrate)
php artisan migrate: fresh

Generates the Administrator User records and a test one
php artisan db: seed


## running in heroku

[Click Here] (https://radiant-earth-84938.herokuapp.com)

## heroku CLI to go the bash

`$ heroku git: remote -a dnc

`heroku run bash
php artisan migrate: refresh
php artisan db: seed