import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#00aaff',
                secondary: '#00ffaa',
                accent: '#7b00ff',
                dark: {
                    bg: '#1a1a2e',
                    text: '#d1d5db',
                },
                light: {
                    bg: '#ffffff',
                    text: '#1f2937',
                },
            },
            backgroundImage: {
                'gradient-tech': 'linear-gradient(135deg, #00aaff 0%, #7b00ff 100%)',
            },
        },
    },
    plugins: [forms, typography],
    build: {
        rollupOptions: {
            external: ['alpinejs', '@alpinejs/focus']
        }
    }
};