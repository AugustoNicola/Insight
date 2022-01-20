module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    safelist: [
        "max-h-max",
        "p-4",
        "border-b-[3px]",
        "lg:border-l-[3px]",
        "bg-primariomedio",
        "border-primario",
    ],
    theme: {
        colors: {
            primario: "#075E52",
            primariohover: "#0A3B34",
            primariopastel: "#D2E8E8",
            primariomedio: "#8CBBBB",
            beige: "#647C76",
            azul: "#30557D",
            celeste: "#6587B2",
            fondo: "#C9C9C9",
            blanco: "#FFFFFF",
            blancohover: "#B0B0B0",
            negro: "#2C2C2C",
            negrohover: "#494949",
        },
        fontFamily: {
            titulo: ["Domine", "sans-serif"],
            texto: ["Noticia Text", "serif"],
            ui: ["Libre Franklin", "sans-serif"],
        },
        extend: {},
    },
    plugins: [],
};
