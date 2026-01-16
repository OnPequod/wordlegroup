import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './app/View/Components/**/*.php',
        './app/Http/Livewire/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        fontFamily: {
            sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'],
            system: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'],
            serif: ['IBM Plex Serif', 'serif'],
            fixed: ['monospace'],
        },
        extend: {
            colors: {
                current: 'currentColor',
                'wordle-yellow': '#C8B458',
                'wordle-green': '#03B203',
                gold: '#FFD700',
                silver: '#C0C0C0',
                bronze: '#CD7F32',
            },
            minHeight: {
                10: '2.5rem',
                11: '2.75rem',
            },
        },
    },
    plugins: [forms, typography],
};
