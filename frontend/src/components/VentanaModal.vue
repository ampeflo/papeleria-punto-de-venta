<!--
  Ventana modal reutilizable (Bootstrap).
  Uso:
    <VentanaModal ref="modal" titulo="Nuevo producto"> ...contenido... </VentanaModal>
    modal.value.abrir()   /   modal.value.cerrar()
-->
<template>
  <Teleport to="body">
    <div ref="el" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" :class="tamano">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ titulo }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <slot />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Modal } from 'bootstrap'

defineProps({
  titulo: String,
  tamano: { type: String, default: '' }, // 'modal-sm', 'modal-lg'...
})

const el = ref(null)
let modal = null

onMounted(() => { modal = new Modal(el.value) })
onBeforeUnmount(() => { modal.hide(); modal.dispose() })

// Funciones que puede usar la vista que contiene este componente
defineExpose({
  abrir: () => modal.show(),
  cerrar: () => modal.hide(),
})
</script>
