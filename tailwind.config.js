export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,jsx,ts,tsx,vue}',
  ],
  theme: {
    extend: {
      colors: {
        'brand': {
          'cream': '#faf9f7',
          'dark': '#0a0a0a',
          'orange': '#ff5722',
        }
      },
      fontFamily: {
        'serif': ['Georgia', 'Garamond', 'serif'],
      },
    },
  },
  plugins: [],
};
