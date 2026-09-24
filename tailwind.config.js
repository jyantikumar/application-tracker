import defaultTheme from 'tailwindcss/defaultTheme';
import flyonui from 'flyonui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flyonui/dist/js/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', 'sans-serif'],
            },
            colors: {
                brand: {
                    DEFAULT: '#92BA56',
                    hover: '#82a74a',
                }
            }
        },
    },

    plugins: [
        flyonui,
    ],

    flyonui: {
        themes: [
            {
                appTheme: {
                    "primary": "#92BA56",
                    "primary-content": "#0B1120",
                    "secondary": "#374151",
                    "accent": "#92BA56",
                    "neutral": "#1F2937",
                    "base-100": "#111827", // Card and container backgrounds
                    "base-200": "#1F2937", // Table headers and select inputs
                    "base-300": "#374151", // Borders and dividers
                    "base-content": "#F3F4F6", // Primary text color
                },
            },
            "dark"
        ],
    },
};