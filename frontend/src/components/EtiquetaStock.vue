<!-- Etiqueta de color según el stock: rojo agotado, amarillo bajo, verde bien -->
<template>
  <span v-if="stock == 0" class="badge text-bg-danger">Agotado</span>
  <span v-else-if="Number(stock) <= Number(minimo)" class="badge text-bg-warning">{{ stock }} pzas</span>
  <span v-else class="badge bg-success-subtle text-success-emphasis">{{ stock }} pzas</span>
</template>

<script setup>
defineProps({
  stock: [Number, String],
  minimo: [Number, String],
})
</script>
