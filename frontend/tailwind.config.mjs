/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        primary: "#4F46E5",
        secondary: "#6366F1",
        accentGreen: "#22C55E",
        accentRed: "#EF4444",
        backgroundGray: "#F9FAFB",
        cardWhite: "#FFFFFF",
        textDark: "#111827",
      },
      backgroundImage: {
        "gradient-primary": "linear-gradient(to right, #4F46E5, #6366F1)",
        "gradient-secondary": "linear-gradient(to right, #6366F1, #22C55E)",
      },
    },
  },
  plugins: [],
};
