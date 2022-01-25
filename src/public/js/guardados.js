let botonesDesguardar = document.getElementsByClassName("boton-desguardar");

for (let i = 0; i < botonesDesguardar.length; i++) {
    botonesDesguardar[i].addEventListener("click", function (evento) {
        //# eliminar guardado de publicacion
        let idPublicacion = this.previousElementSibling.value;

        // hacemos la request
        fetch("/api/reacciones", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": document.getElementsByName("_token")[0].value,
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
                    this.parentNode.parentNode.parentNode.removeChild(
                        this.parentNode.parentNode
                    );
                    break;

                default:
                    break;
            }
        });
    });
}
