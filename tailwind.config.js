/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue", // Instantly tells Tailwind to read your Vue files
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
