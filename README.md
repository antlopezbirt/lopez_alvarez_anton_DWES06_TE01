## DWES06 TE01: Laravel + Microservicio Java con Spring Boot

### Acciones iniciales

GitHub añade un sufijo "-main" al nombre del directorio al descargarlo como ZIP. Se recomienda eliminar ese sufijo y sacar el directorio lopez_alvarez_anton_DWES06_TE01 de otro de igual nombre que el extractor puede generar, de modo que la ruta hasta el proyecto en el disco (en Windows) quede así, para facilitar las pruebas con la colección de Postman que se incluye:

```bash
C:\{ruta hasta htdocs}\lopez_alvarez_anton_DWES06_TE01\
```

Se han incluido en el repositorio las dependencias de ```vendor``` y el fichero de entorno ```.env```, comentando algunas líneas en gitignore. No obstante se recomienda antes de nada ejecutar Composer por si acaso:

```bash
cd coleccionMusical
composer install
```

### Datos incluidos: Laravel y Spring Boot

Para Laravel se han incluido los datos iniciales dentro de las migraciones.

Para Spring Boot se ha generado un script SQL que se deberá ejecutar para crear la base de datos dedicada a él con los datos correspondientes.

Por ello, se recomienda ejecutar las migraciones, que generarán dichas tablas con sus datos:

```bash
php artisan migrate
```

Y posteriormente importar y ejecutar en el gestor de bases de datos el script SQL de Spring Boot.

En este punto se puede pasar a probar los endpoints en Postman.