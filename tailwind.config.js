import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Work Sans', 'system-ui', 'sans-serif'],
            },
            colors: {
                primary: '#2563EB',
                secondary: '#64748B',
                accent: '#38BDF8',
                background: '#F9FAFB',
                heading: '#111827',
                body: '#374151',
                success: '#22C55E',
                warning: '#FACC15',
                error: '#EF4444',
            },
        },
        screens: {
            'xs': '480px',
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
            '3xl': '1920px',
            '4xl': '2560px',
            '5xl': '3840px',
        }
    },

    plugins: [forms],
};
