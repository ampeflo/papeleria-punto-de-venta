<!-- Categorías: todos consultan; solo el administrador crea, edita y elimina -->
<template>
  <div>
    <div v-if="esAdmin" class="d-flex mb-3">
      <button class="btn btn-primary ms-auto" @click="nuevo"><i class="bi bi-plus-lg"></i> Nueva categoría</button>
    </div>

    <div class="card" style="max-width: 700px">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Nombre</th><th class="text-center">Productos</th><th v-if="esAdmin"></th></tr></thead>
          <tbody>
            <tr v-for="c in categorias" :key="c.id_categoria">
              <td class="fw-semibold">{{ c.nombre }}</td>
              <td class="text-center">{{ c.num_productos }}</td>
              <td v-if="esAdmin" class="acciones">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar" @click="editar(c)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar" @click="eliminar(c)"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <VentanaModal ref="modal" :titulo="idEditando ? 'Editar categoría' : 'Nueva categoría'" tamano="modal-sm">
      <form ref="formulario" :class="{ 'was-validated': validado }" novalidate @submit.prevent="guardar">
        <div class="modal-body">
          <label class="form-label requerido" for="nombre">Nombre</label>
          <input id="nombre" v-model.trim="nombre" class="form-control" required minlength="3" maxlength="50">
          <div class="invalid-feedback">Entre 3 y 50 caracteres.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </VentanaModal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../services/api'
import { avisar } from '../services/avisos'
import { esAdmin } from '../services/sesion'
import VentanaModal from '../components/VentanaModal.vue'

const categorias = ref([])
const modal = ref(null)
const formulario = ref(null)
const validado = ref(false)
const idEditando = ref(null)
const nombre = ref('')

onMounted(cargar)

async function cargar() {
  try {
    categorias.value = (await api('/categorias')).data
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

function nuevo() {
  idEditando.value = null
  nombre.value = ''
  validado.value = false
  modal.value.abrir()
}

function editar(categoria) {
  idEditando.value = categoria.id_categoria
  nombre.value = categoria.nombre
  validado.value = false
  modal.value.abrir()
}

async function guardar() {
  validado.value = true
  if (!formulario.value.checkValidity()) return

  try {
    const datos = { nombre: nombre.value }
    const respuesta = idEditando.value
      ? await api('/categorias/' + idEditando.value, 'PUT', datos)
      : await api('/categorias', 'POST', datos)
    avisar(respuesta.mensaje)
    modal.value.cerrar()
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

async function eliminar(categoria) {
  if (!confirm(`¿Eliminar la categoría "${categoria.nombre}"?`)) return
  try {
    avisar((await api('/categorias/' + categoria.id_categoria, 'DELETE')).mensaje)
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}
</script>
