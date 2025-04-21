/** @type {import('tailwindcss').Config} */
export default {
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.vue",
      "./resources/**/*.js",
    ],
    theme: {
      extend: {
        colors: {
          secondary: "#003466",
          primary: "#FF830F",
        }
      },
    },
    plugins: [],
  };
  