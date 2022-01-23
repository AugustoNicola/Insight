let botonMeGusta = document.getElementById("boton-megusta");
let botonGuardar = document.getElementById("boton-guardar");
let idPublicacion = document.getElementById("publicacion-id").value;
let contadorMeGusta = document.getElementById("contador-megusta");

botonMeGusta.addEventListener("click", function (evento) {
    if (!this.classList.contains("activado")) {
        //# me gusta

        // hacemos la request
        fetch("/api/reacciones", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token":
                    document.getElementsByName("_token")[0].value,
            },
            credentials: "same-origin",
            body: JSON.stringify({
                publicacion: idPublicacion,
                relacion: "me_gusta",
            }),
        }).then((respuesta) => {
            // respuesta obtenida
            switch (respuesta.status) {
                case 200:
                    //* reaccion cargada exitosamente
                    this.classList.add("activado");
                    this.children[0].classList.add("text-primario", "bxs-heart");
                    this.children[0].classList.remove("text-negro", "bx-heart");
                    let cantidadMeGusta =
                        parseInt(contadorMeGusta.dataset.cantidad) + 1;
                    contadorMeGusta.setAttribute(
                        "data-cantidad",
                        cantidadMeGusta
                    );
                    contadorMeGusta.innerHTML =
                        cantidadMeGusta + " me gusta";
                    break;

                case 401:
                    //? usuarie no autenticade, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=auth"
                    );
                    break;

                case 404:
                    //? publicacion no encontrada, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=publicacion"
                    );
                    break;

                default:
                    //? ocurrio algun error desconocido
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=desconocido"
                    );
                    break;
            }
        });
    } else {
        //# no me gusta

        // hacemos la request
        fetch("/api/reacciones", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token":
                    document.getElementsByName("_token")[0].value,
            },
            credentials: "same-origin",
            body: JSON.stringify({
                publicacion: idPublicacion,
                relacion: "me_gusta",
            }),
        }).then((respuesta) => {
            // respuesta obtenida
            switch (respuesta.status) {
                case 200:
                    //* reaccion eliminada exitosamente
                    this.classList.remove("activado");
                    this.children[0].classList.remove("text-primario", "bxs-heart");
                    this.children[0].classList.add("text-negro", "bx-heart");
                    let cantidadMeGusta =
                        parseInt(contadorMeGusta.dataset.cantidad) - 1;
                    contadorMeGusta.setAttribute(
                        "data-cantidad",
                        cantidadMeGusta
                    );
                    contadorMeGusta.innerHTML =
                        cantidadMeGusta + " me gusta";
                    break;

                case 401:
                    //? usuarie no autenticade, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=auth"
                    );
                    break;

                case 404:
                    //? publicacion no encontrada, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=publicacion"
                    );
                    break;

                default:
                    //? ocurrio algun error desconocido
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=desconocido"
                    );
                    break;
            }
        });
    }
});



botonGuardar.addEventListener("click", function (evento) {
    if (!this.classList.contains("activado")) {
        //# me gusta

        // hacemos la request
        fetch("/api/reacciones", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token":
                    document.getElementsByName("_token")[0].value,
            },
            credentials: "same-origin",
            body: JSON.stringify({
                publicacion: idPublicacion,
                relacion: "guardar",
            }),
        }).then((respuesta) => {
            // respuesta obtenida
            switch (respuesta.status) {
                case 200:
                    //* reaccion cargada exitosamente
                    this.classList.add("activado");
                    this.children[0].classList.add("text-primario", "bxs-bookmark");
                    this.children[0].classList.remove("text-negro", "bx-bookmark");
                    break;

                case 401:
                    //? usuarie no autenticade, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=auth"
                    );
                    break;

                case 404:
                    //? publicacion no encontrada, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=publicacion"
                    );
                    break;

                default:
                    //? ocurrio algun error desconocido
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=desconocido"
                    );
                    break;
            }
        });
    } else {
        //# no me gusta

        // hacemos la request
        fetch("/api/reacciones", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token":
                    document.getElementsByName("_token")[0].value,
            },
            credentials: "same-origin",
            body: JSON.stringify({
                publicacion: idPublicacion,
                relacion: "guardar",
            }),
        }).then((respuesta) => {
            // respuesta obtenida
            switch (respuesta.status) {
                case 200:
                    //* reaccion eliminada exitosamente
                    this.classList.remove("activado");
                    this.children[0].classList.remove("text-primario", "bxs-bookmark");
                    this.children[0].classList.add("text-negro", "bx-bookmark");
                    break;

                case 401:
                    //? usuarie no autenticade, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=auth"
                    );
                    break;

                case 404:
                    //? publicacion no encontrada, mostrar error
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=publicacion"
                    );
                    break;

                default:
                    //? ocurrio algun error desconocido
                    // los errores se manejan desde el controlador leyendo el query string
                    window.location.replace(
                        location.protocol +
                            "//" +
                            location.host +
                            location.pathname +
                            "?error=desconocido"
                    );
                    break;
            }
        });
    }
});
