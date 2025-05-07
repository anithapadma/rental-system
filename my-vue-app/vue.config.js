const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  
  // Add dev server proxy configuration for API requests
  devServer: {
    proxy: {
      '^/api': {
        target: process.env.VUE_APP_API_URL || 'http://localhost:8000/api',
        changeOrigin: true
      },
      '^/rentals': {
        target: process.env.VUE_APP_API_URL || 'http://localhost:8000/api',
        pathRewrite: { '^/rentals': '/rentals' },
        changeOrigin: true
      }
    }
  }
})
