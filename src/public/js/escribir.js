let inputImagen = document.getElementById("inputImagen");
let previewImagen = document.getElementById("previewImagen");

inputImagen.addEventListener("change", function (evento) {
    let [file] = this.files;
    if (file) {
        previewImagen.src = URL.createObjectURL(file);
    }
});
