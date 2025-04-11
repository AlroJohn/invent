/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [],
  theme: {
    extend: {},
  },
  plugins: [],
}
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './user/products.php', // Include your PHP files
    './path/to/other/files/**/*.{html,js}', // Adjust to your file structure
  ],
  theme: {
    extend: {
      animation: {
        highlight: 'highlight 1.5s ease-in-out',
      },
      keyframes: {
        highlight: {
          '0%': { backgroundColor: '#D1FAE5' }, // Light green
          '100%': { backgroundColor: 'transparent' },
        },
      },
    },
  },
  plugins: [],
};

