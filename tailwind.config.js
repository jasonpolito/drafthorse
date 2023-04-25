const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",
    './vendor/filament/**/*.blade.php',
  ],
  purge: {
		options: {
			safelist: 
      [{
        pattern: /^w-/,
        variants: ['xl', 'lg', 'md', 'sm'],
      }],
		}
  },
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        danger: colors.rose,
        // primary: colors.lime,
        primary: colors.green,
        success: colors.green,
        warning: colors.yellow,
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ],
}
