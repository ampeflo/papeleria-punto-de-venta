import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig(({ command }) => ({
  // vueDevTools: botón flotante para inspeccionar componentes, rutas y estado
  // (solo aparece con "npm run dev", no en la versión compilada)
  plugins: [vue(), vueDevTools()],

  // Al compilar (npm run build), Apache servirá la app desde esta carpeta
  base: command === 'build' ? '/papeleria/frontend/dist/' : '/',

  server: {
    port: 5173,
    // PROXY: en desarrollo, todo lo que empiece con /api se reenvía al backend PHP en Apache.
    // Así el navegador cree que frontend y backend están en el mismo sitio (sin problemas de CORS).
    proxy: {
      '/api': {
        target: 'http://localhost',
        rewrite: (ruta) => '/papeleria/backend/public' + ruta,
      },
    },
  },
}))
