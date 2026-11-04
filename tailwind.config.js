/** @type {import('tailwindcss').Config} */

module.exports = {
    // darkMode: 'false',
    // Never scan assets/vendor/ (importmap downloads): third-party JS would
    // generate hundreds of unused classes.
    content: [
        "./assets/*.js",
        "./assets/controllers/**/*.js",
        "./assets/js/**/*.{js,vue}",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {
            backgroundImage: {
                'hero': "linear-gradient(rgba(248, 250, 252, .8), rgba(248, 250, 252, 0.6), rgba(248, 250, 252, 1))",
                'hero-laptop': "url('../images/hero/hero_laptop_jphiweb.webp')",
                'hero-laptop-vertical': "url('../images/hero/hero_laptop_vertical_jphiweb.webp')",
                'base_share': "url('../images/jphiweb_facebook_image.webp')",
                'hero-mobile': "url('../images/st-antoine-sart-messire-guillaume.webp')",
                'hero-md': "url('../images/st-antoine-sart-messire-guillaume.webp')",
                'logo-light': "url('../images/logo/logo-jphiweb-light.webp')",
                'logo-dark': "url('../images/logo/logo-jphiweb-dark-pink.webp')",
                'index-think': "url('../images/home/think_jphiweb_be.webp')",
                'index-think-dark': "url('../images/home/think_jphiweb_be_white.png')",
                // 'hero-mobile': "url('../images/abbaye2.webp')",
                // 'hero-md': "url('../images/abbaye2.webp')",
            },
            transitionProperty: {
                'height': 'height',
                'spacing': 'margin, padding',
            },
            fontFamily: {
                "lato": ["Lato", "sans-serif"],
                "kalam": ["Kalam", "sans-serif"],
            },
            // colors: {
            //   "transparent": "transparent",
            //   "white": "#E8F3FF",
            //   "base": {
            //     "light": "#CBD3DC",
            //     "dark": "#1C2024",
            //   },
            //   "surface": {
            //     "light": "#f4faff",
            //     "dark": "#283440",
            //     "secondary": "#1D40AF",
            //   },
            //   "content": {
            //     "primary": {
            //       "light": "#040D1C",
            //       "dark": "#E8F3FF",
            //     },
            //     "secondary": "#1D40AF",
            //     "highlight":{
            //       "light": "#3B7C45",
            //       "dark": "#F2C029"
            //     },
            //   },
            //   "secondary": "#3B7C45", // Jaune vif pour les éléments de mise en avant
            //
            // },
        },
    },
    plugins: [
        require("@tailwindcss/typography"),
        require('daisyui')
    ],
    daisyui: {
        themes: [
            {
                corporate: {
                    ...require("daisyui/src/theming/themes")["corporate"],
                    primary: "#1732AB",
                },
            },
            // 'corporate',
            'dracula'],
        darkTheme: "dracula",
    }
}
