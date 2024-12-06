import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    port: 3000,
    watch: {
      usePolling: false,
      interval: 5000
    },
    hmr: {
      protocol: 'ws',
      host: 'localhost',
      port: 3000
    }
  }
})
