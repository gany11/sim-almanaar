/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/layout/**/*.{php,html,js}",
    "./app/Views/admin/**/*.{php,html,js}",
    "./app/Views/tv/**/*.{php,html,js}",
    "./app/Views/landing/**/*.{php,html,js}"
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