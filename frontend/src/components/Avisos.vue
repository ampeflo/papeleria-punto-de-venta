<!-- Muestra los mensajes flotantes que se crean con avisar() -->
<template>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div v-for="a in avisos" :key="a.id" class="toast show border-0" :class="'text-bg-' + a.tipo">
      <div class="d-flex">
        <div class="toast-body">{{ a.mensaje }}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="cerrarAviso(a.id)"></button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { avisos, cerrarAviso } from '../services/avisos'
</script>
