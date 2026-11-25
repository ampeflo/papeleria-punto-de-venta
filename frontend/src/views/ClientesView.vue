<!-- Clientes: todos consultan; solo el administrador crea, edita y elimina -->
<template>
  <div>
    <div class="d-flex flex-wrap gap-2 mb-3">
      <input v-model="buscar" type="search" class="form-control" style="max-width: 280px"
             placeholder="Buscar por nombre o teléfono" @input="cargar">
      <button v-if="esAdmin" class="btn btn-primary ms-auto" @click="nuevo"><i class="bi bi-plus-lg"></i> Nuevo cliente</button>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Nombre</th><th>Teléfono</th><th>Correo</th><th class="text-center">Compras</th><th v-if="esAdmin"></th></tr></thead>
          <tbody>
            <tr v-for="c in clientes" :key="c.id_cliente">
              <td class="fw-semibold">{{ c.nombre }}</td>
              <td>{{ c.telefono || '—' }}</td>
              <td class="small">{{ c.email || '—' }}</td>
              <td class="text-center">{{ c.num_compras }}</td>
              <td v-if="esAdmin" class="acciones">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar" @click="editar(c)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar" @click="eliminar(c)"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr v-if="!clientes.length"><td colspan="5" class="vacio">No se encontraron clientes</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <VentanaModal ref="modal" :titulo="idEditando ? 'Editar cliente' : 'Nuevo cliente'">
      <form ref="formulario" :class="{ 'was-validated': validado }" novalidate @submit.prevent="guardar">
        <div class="modal-body row g-3">
          <div class="col-12">
            <label class="form-label requerido" for="nombre">Nombre</label>
            <input id="nombre" v-model="form.nombre" class="form-control" required minlength="3" maxlength="100">
            <div class="invalid-feedback">Mínimo 3 caracteres.</div>
          </div>
          <div class="col-sm-5">
            <label class="form-label" for="telefono">Teléfono</label>
            <input id="telefono" v-model="form.telefono" class="form-control" pattern="\d{10}" maxlength="10" placeholder="10 dígitos">
            <div class="invalid-feedback">Debe tener 10 dígitos.</div>
          </div>
          <div class="col-sm-7">
            <label class="form-label" for="email">Correo</label>
            <input id="email" v-model="form.email" type="email" class="form-control" maxlength="100">
            <div class="invalid-feedback">Correo no válido.</div>
          </div>
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

const clientes = ref([])
const buscar = ref('')
const modal = ref(null)
const formulario = ref(null)
const validado = ref(false)
const idEditando = ref(null)
const form = ref({ nombre: '', telefono: '', email: '' })

onMounted(cargar)

async function cargar() {
  try {
    clientes.value = (await api('/clientes?buscar=' + encodeURIComponent(buscar.value))).data
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

function nuevo() {
  idEditando.value = null
  form.value = { nombre: '', telefono: '', email: '' }
  validado.value = false
  modal.value.abrir()
}

function editar(cliente) {
  idEditando.value = cliente.id_cliente
  form.value = { nombre: cliente.nombre, telefono: cliente.telefono ?? '', email: cliente.email ?? '' }
  validado.value = false
  modal.value.abrir()
}

async function guardar() {
  validado.value = true
  if (!formulario.value.checkValidity()) return

  try {
    const respuesta = idEditando.value
      ? await api('/clientes/' + idEditando.value, 'PUT', form.value)
      : await api('/clientes', 'POST', form.value)
    avisar(respuesta.mensaje)
    modal.value.cerrar()
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

async function eliminar(cliente) {
  if (!confirm(`¿Eliminar a "${cliente.nombre}"?`)) return
  try {
    avisar((await api('/clientes/' + cliente.id_cliente, 'DELETE')).mensaje)
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}
</script>
