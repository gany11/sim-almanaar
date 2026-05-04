/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
  ],
  safelist: [
    {
      pattern: /(bg|text)-(gray|orange|emerald|rose|amber|blue|indigo|violet|fuchsia|slate|teal|sky)-(100|700)/,
    },
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}