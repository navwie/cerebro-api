import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "tailwindcss";
import autoprefixer from "autoprefixer";
import path from "path";

export default defineConfig({
    define: {
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: true,
    },
    build: {
        minify: true,
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    plugins: [
        vue(),
        laravel({
            input: [
                "resources/css/app.css",
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/js/sitesManagement/common.js",
                "resources/js/sitesManagement/createCardForms.js",
                "resources/js/sitesManagement/createLoanForms.js",
                "resources/js/sitesManagement/editCardForms.js",
                "resources/js/sitesManagement/editLoanForms.js",
                "resources/newapproach/sass/app.scss",
                "resources/newapproach/css/app.css",
                "resources/newapproach/js/app.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "~bootstrap": path.resolve(__dirname, "node_modules/bootstrap")
        },
    },
});
