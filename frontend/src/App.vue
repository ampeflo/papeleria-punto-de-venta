<!--
  Componente raíz.
  - Login: se muestra solo, a pantalla completa.
  - Demás páginas: menú lateral + barra superior + la vista de la ruta actual.
-->
<template>
  <router-view v-if="$route.meta.publica" />

  <div v-else-if="sesion.usuario" class="layout">
    <BarraLateral />
    <div class="contenido">
      <BarraSuperior />
      <main class="pagina">
        <router-view />
      </main>
    </div>
  </div>

  <Avisos />
</template>

<script setup>
import { sesion } from './services/sesion'
import BarraLateral from './components/BarraLateral.vue'
import BarraSuperior from './components/BarraSuperior.vue'
import Avisos from './components/Avisos.vue'
</script>
