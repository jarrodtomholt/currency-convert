const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
    mode: 'jit',
    content: [
        './resources/nuxt/components/**/*.{js,vue,ts}',
        './resources/nuxt/layouts/**/*.vue',
        './resources/nuxt/pages/**/*.vue',
        './resources/nuxt/plugins/**/*.{js,ts}',
        './nuxt.config.{js,ts}',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': colors.cyan,
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
