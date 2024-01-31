/** @type {import('tailwindcss').Config} */

import colors from "tailwindcss/colors";
import path from "path";

module.exports = {
    content: [
        './resources/newapproach/**/*.js',
        './resources/newapproach/**/*.vue',
    ],
    darkMode: "media",
    theme: {
        extend: {
        },
    },
    variants: {
        extend: {},
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require("@tailwindcss/aspect-ratio"),
    ],
};
