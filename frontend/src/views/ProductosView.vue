<!-- Productos: todos consultan; solo el administrador crea, edita y elimina -->
<template>
  <div>
    <div class="d-flex flex-wrap gap-2 mb-3">
      <input v-model="buscar" type="search" class="form-control" style="max-width: 280px"
             placeholder="Buscar por nombre o código" @input="cargar">
      <select v-model="filtroCategoria" class="form-select" style="max-width: 220px" @change="cargar">
        <option value="">Todas las categorías</option>
        <option v-for="c in categorias" :key="c.id_categoria" :value="c.id_categoria">{{ c.nombre }}</option>
      </select>
      <button v-if="esAdmin" class="btn btn-primary ms-auto" @click="nuevo">
        <i class="bi bi-plus-lg"></i> Nuevo producto
      </button>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr><th>Código</th><th>Producto</th><th>Categoría</th><th class="text-end">Precio</th><th class="text-center">Stock</th><th v-if="esAdmin"></th></tr>
          </thead>
          <tbody>
            <tr v-for="p in productos" :key="p.id_producto">
              <td class="small text-nowrap">{{ p.codigo }}</td>
              <td>{{ p.nombre }}</td>
              <td class="small">{{ p.categoria }}</td>
              <td class="text-end fw-semibold">{{ dinero(p.precio) }}</td>
              <td class="text-center"><EtiquetaStock :stock="p.stock" :minimo="p.stock_minimo" /></td>
              <td v-if="esAdmin" class="acciones">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar" @click="editar(p)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar" @click="eliminar(p)"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr v-if="!productos.length"><td colspan="6" class="vacio">No se encontraron productos</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Formulario crear / editar -->
    <VentanaModal ref="modal" :titulo="idEditando ? 'Editar producto' : 'Nuevo producto'">
      <form ref="formulario" :class="{ 'was-validated': validado }" novalidate @submit.prevent="guardar">
        <div class="modal-body row g-3">
          <div class="col-5">
            <label class="form-label requerido" for="codigo">Código</label>
            <input id="codigo" v-model="form.codigo" class="form-control text-uppercase" required maxlength="20">
            <div class="invalid-feedback">Obligatorio.</div>
          </div>
          <div class="col-7">
            <label class="form-label requerido" for="categoria">Categoría</label>
            <select id="categoria" v-model="form.id_categoria" class="form-select" required>
              <option value="">Selecciona...</option>
              <option v-for="c in categorias" :key="c.id_categoria" :value="c.id_categoria">{{ c.nombre }}</option>
            </select>
            <div class="invalid-feedback">Selecciona una.</div>
          </div>
          <div class="col-12">
            <label class="form-label requerido" for="nombre">Nombre</label>
            <input id="nombre" v-model="form.nombre" class="form-control" required minlength="3" maxlength="100">
            <div class="invalid-feedback">Mínimo 3 caracteres.</div>
          </div>
          <div class="col-4">
            <label class="form-label requerido" for="precio">Precio</label>
            <input id="precio" v-model="form.precio" type="number" class="form-control" required min="0.01" step="0.01">
            <div class="invalid-feedback">Mayor a 0.</div>
          </div>
          <div class="col-4">
            <label class="form-label requerido" for="stock">Stock</label>
            <input id="stock" v-model="form.stock" type="number" class="form-control" required min="0" step="1">
            <div class="invalid-feedback">0 o más.</div>
          </div>
          <div class="col-4">
            <label class="form-label requerido" for="minimo">Mínimo</label>
            <input id="minimo" v-model="form.stock_minimo" type="number" class="form-control" required min="0" step="1">
            <div class="invalid-feedback">0 o más.</div>
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
import { dinero } from '../services/formato'
import EtiquetaStock from '../components/EtiquetaStock.vue'
import VentanaModal from '../components/VentanaModal.vue'

const productos = ref([])
const categorias = ref([])
const buscar = ref('')
const filtroCategoria = ref('')

// Formulario
const modal = ref(null)
const formulario = ref(null)
const validado = ref(false)
const idEditando = ref(null)   // null = producto nuevo
const vacio = { codigo: '', nombre: '', id_categoria: '', precio: '', stock: '', stock_minimo: 5 }
const form = ref({ ...vacio })

onMounted(async () => {
  try {
    categorias.value = (await api('/categorias')).data
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
})

async function cargar() {
  productos.value = (await api(`/productos?buscar=${encodeURIComponent(buscar.value)}&categoria=${filtroCategoria.value}`)).data
}

function nuevo() {
  idEditando.value = null
  form.value = { ...vacio }
  validado.value = false
  modal.value.abrir()
}

function editar(producto) {
  idEditando.value = producto.id_producto
  form.value = { ...producto }   // copia, para no cambiar la tabla mientras se edita
  validado.value = false
  modal.value.abrir()
}

async function guardar() {
  validado.value = true
  if (!formulario.value.checkValidity()) return

  try {
    const respuesta = idEditando.value
      ? await api('/productos/' + idEditando.value, 'PUT', form.value)
      : await api('/productos', 'POST', form.value)
    avisar(respuesta.mensaje)
    modal.value.cerrar()
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

async function eliminar(producto) {
  if (!confirm(`¿Eliminar "${producto.nombre}"?`)) return
  try {
    avisar((await api('/productos/' + producto.id_producto, 'DELETE')).mensaje)
    await cargar()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}
</script>
