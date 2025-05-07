const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  
  // Add dev server proxy configuration for API requests
  devServer: {
    proxy: {
      '^/api': {
        target: 'http://localhost:8000',
        changeOrigin: true
      },
      '^/rentals': {
        target: 'http://localhost:8000/api',
        pathRewrite: { '^/rentals': '/rentals' },
        changeOrigin: true
      }
    }
  }
})
