import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#2563EB',
                secondary: '#3B82F6',
                accent: '#059669',
                background: '#F8FAFC',
                foreground: '#0F172A',
                card: '#FFFFFF',
                'card-foreground': '#0F172A',
                muted: '#F1F5FD',
                'muted-foreground': '#475569',
                border: '#E4ECFC',
                destructive: '#DC2626',
                ring: '#2563EB',
            },
            fontFamily: {
                sans: ['Atkinson Hyperlegible', ...defaultTheme.fontFamily.sans],
                serif: ['Crimson Pro', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
