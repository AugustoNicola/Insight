![Insight](Logo.png)
### *Proyecto de blog de noticias hecho con el framework Laravel*

### **Versión en línea: https://insight-noticias.herokuapp.com//**

![Screenshot](screenshot.png)

<br>

### [English README here :uk: :us:](README-EN.md)

<br>

Insight es una aplicación full-stack para leer y escribir notas de blog, con funcionalidades como creación de usuaries, subida de archivos, sistema de *likes*, comentarios y guardados, categorías de publicación, filtro de búsqueda y edición de publicaciones.

Tanto el *frontend* como *backend* están hechos con el framework de PHP, [Laravel](https://laravel.com/docs/8.x) `v.8.81.0`, conectado a una base de datos [MySQL](https://www.mysql.com/). El *frontend* utiliza además [TailwindCSS](https://tailwindcss.com/docs/) para los estilos CSS.

Para realizar gran parte del código usé la metodología de TDD (*Test-Driven Development*), que implica crear las pruebas a pasar antes que el código en sí. Estas pruebas están disponibles en `tests/Feature` y pueden ser ejecutadas con el comando de artisan: `php artisan test`.

Debido a las [limitaciones del hosting gratuito de Heroku](https://devcenter.heroku.com/articles/active-storage-on-heroku#ephemeral-disk) que no permiten almacenar archivos en el disco del Dyno, es imposible mantener las imágenes cargadas por usuaries. Mi solución para poder seguir usando este hosting es el de seedear la base de datos al reiniciarse el Dyno (después de un período de inactividad) con `php artisan db:seed`. De esta forma les usuaries pueden usar la página con todas las funcionalidades mientras el Dyno siga activo.

<br />

# Contribuciones y Licencia
Este proyecto está bajo la [Licencia MIT](https://choosealicense.com/licenses/mit/). **¡Podés leer, usar o modificar el código que necesites!**

Cualquier aporte de código, notificación de errores o fallas, sugerencias o cualquier otro tipo de contribución será enormemente agradecida. 

¡Espero que te guste mi trabajo! :+1: