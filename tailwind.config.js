import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./admin-vue/**/*.{vue,js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                "fade-in-down": {
                    from: {
                        transform: "translateY(-0.75rem)",
                        opacity: 0,
                    },
                    to: {
                        transform: "translateY(0rem)",
                        opacity: 1,
                    },
                },
            },
            animation: {
                fadeIn: "fadeIn 0.5s ease-in-out",
                "fade-in-down": "fade-in-down 0.5s ease-in-out both",
            },
        },
    },
    plugins: [],
};
