import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "admin-vue/src/style.css",
                "admin-vue/src/main.js",
            ],
            refresh: true,
        }),
        vue(),
    ],
});
