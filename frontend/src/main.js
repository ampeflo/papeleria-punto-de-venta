// Punto de entrada de la aplicación Vue
import { createApp } from 'vue'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap'                 // JavaScript de Bootstrap (modales y menú en celular)
import './assets/estilos.css'

import App from './App.vue'
import router from './router'

createApp(App).use(router).mount('#app')
