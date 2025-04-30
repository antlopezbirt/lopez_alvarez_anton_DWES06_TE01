## DWES06 TE01: Laravel + Microservicio Java con Spring Boot

### Acciones iniciales

GitHub añade un sufijo "-main" al nombre del directorio al descargarlo como ZIP. Se recomienda eliminar ese sufijo y sacar el directorio lopez_alvarez_anton_DWES06_TE01 de otro de igual nombre que el extractor puede generar, de modo que la ruta hasta el proyecto Laravel en el disco (en Windows) quede así, para facilitar las pruebas con la colección de Postman que se incluye:

```bash
C:\{ruta hasta htdocs}\lopez_alvarez_anton_DWES05_TE01\coleccionMusical
```

Se han incluido en el repositorio las dependencias de ```vendor``` y el fichero de entorno ```.env```, comentando algunas líneas en gitignore. No obstante se recomienda antes de nada ejecutar Composer por si acaso:

```bash
cd coleccionMusical
composer install
```

### Datos incluidos

Se han incluido los datos iniciales dentro de las migraciones. De este modo se puede probar la aplicación con el mismo conjunto de datos que en el vídeo de autoevaluación sin necesidad de ejecutar ningún script SQL.

Por ello, se recomienda ejecutar las migraciones, que generarán dichas tablas con sus datos:

```bash
php artisan migrate
```

Ahora sí se puede pasar a probar los endpoints con Postman. Si por cualquier motivo se quiere empezar de nuevo:

```bash
php artisan migrate:refresh
```