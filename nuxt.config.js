require('dotenv').config()

export default {
  ssr: false,
  srcDir: './resources/nuxt/',
  head: {
    title: 'Currency Converter : five9 Tech Test',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ],
      bodyAttrs: {
          class: 'min-h-screen bg-gray-50'
      }
  },

  css: [
      '~/assets/css/main.css',
  ],

  plugins: [
      { src: '~/plugins/axios.js', ssr: false },
  ],

  components: true,

  buildModules: [
      '@nuxtjs/tailwindcss',
  ],

  modules: [
      '@nuxtjs/axios',
  ],

  axios: {
    baseURL: (`${process.env.APP_URL}` ?? 'http://localhost:8000/')
        .replace(/\/+$/, '/api/'),
  },

  build: {
  }
}
