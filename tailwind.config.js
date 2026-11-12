import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: 'rgb(var(--primary) / <alpha-value>)',
                'primary-dark': 'rgb(var(--primary-dark) / <alpha-value>)',
                'primary-foreground': 'rgb(var(--primary-foreground) / <alpha-value>)',
                brand: 'rgb(var(--brand) / <alpha-value>)',
                'brand-foreground': 'rgb(var(--brand-foreground) / <alpha-value>)',
                secondary: 'rgb(var(--secondary) / <alpha-value>)',
                accent: 'rgb(var(--accent) / <alpha-value>)',
                background: 'rgb(var(--background) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                foreground: 'rgb(var(--foreground) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
                'muted-foreground': 'rgb(var(--muted-foreground) / <alpha-value>)',
                border: 'rgb(var(--border) / <alpha-value>)',
                success: 'rgb(var(--success) / <alpha-value>)',
                'success-foreground': 'rgb(var(--success-foreground) / <alpha-value>)',
                warning: 'rgb(var(--warning) / <alpha-value>)',
                danger: 'rgb(var(--danger) / <alpha-value>)',
                'danger-foreground': 'rgb(var(--danger-foreground) / <alpha-value>)',
                destructive: 'rgb(var(--danger) / <alpha-value>)',
                ring: 'rgb(var(--ring) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                sm: '6px',
                DEFAULT: '8px',
                md: '10px',
                lg: '12px',
                xl: '14px',
                '2xl': '16px',
            },
            boxShadow: {
                card: '0 1px 2px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.08)',
                pop: '0 10px 30px -12px rgba(15, 23, 42, 0.25)',
            },
        },
    },

    plugins: [forms],
};
