let botonUsuarie = document.getElementById("boton-usuarie");
let navegacion = document.getElementById("navegacion");

botonUsuarie.addEventListener("click", function (evento) {
    // desplegar navegacion
    if (!navegacion.classList.contains("activado")) {
        navegacion.classList.remove("max-h-0");
        navegacion.classList.add(
            "max-h-max",
            "p-4",
            "bg-primariomedio",
            "border-primario",
            "border-b-[3px]",
            "lg:border-l-[3px]",
            "activado"
        );
    } else {
        // guardar navegacion
        navegacion.classList.add("max-h-0");
        navegacion.classList.remove(
            "max-h-max",
            "p-4",
            "bg-primariomedio",
            "border-primario",
            "border-b-[3px]",
            "lg:border-l-[3px]",
            "activado"
        );
    }
});
