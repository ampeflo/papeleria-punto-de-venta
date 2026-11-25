<!--
  Nueva venta
  1. Clic en un producto -> se agrega al carrito.
  2. "Cobrar" -> POST /api/ventas. El backend guarda todo en una transacción y descuenta el stock.
-->
<template>
  <div class="row g-3">
    <!-- Productos -->
    <div class="col-lg-7 col-xl-8">
      <div class="card h-100">
        <div class="card-body">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input v-model="buscar" type="search" class="form-control" placeholder="Buscar producto por nombre o código" @input="cargarProductos">
          </div>

          <div class="productos-grid">
            <button v-for="p in productos" :key="p.id_producto" class="producto-tarjeta"
                    :disabled="p.stock == 0" @click="agregar(p)">
              <span class="codigo">{{ p.codigo }}</span>
              <span class="nombre">{{ p.nombre }}</span>
              <span class="d-flex justify-content-between align-items-center mt-auto">
                <span class="precio">{{ dinero(p.precio) }}</span>
                <EtiquetaStock :stock="p.stock" :minimo="p.stock_minimo" />
              </span>
            </button>
          </div>
          <p v-if="!productos.length" class="text-muted">No se encontraron productos.</p>
        </div>
      </div>
    </div>

    <!-- Carrito y cobro -->
    <div class="col-lg-5 col-xl-4">
      <div class="card">
        <div class="card-header"><i class="bi bi-cart3 me-1"></i> Venta actual</div>
        <div class="card-body">
          <div v-for="item in carrito" :key="item.id_producto" class="carrito-item">
            <div class="info">
              <div class="nombre">{{ item.nombre }}</div>
              <div class="small text-muted">{{ dinero(item.precio) }} c/u</div>
            </div>
            <input v-model.number="item.cantidad" type="number" class="form-control form-control-sm"
                   min="1" :max="item.stock" @change="corregirCantidad(item)">
            <div class="fw-semibold text-end" style="width: 75px">{{ dinero(item.precio * item.cantidad) }}</div>
            <button class="btn btn-sm btn-link text-danger" @click="quitar(item)"><i class="bi bi-x-lg"></i></button>
          </div>
          <p v-if="!carrito.length" class="text-center text-muted py-4">Haz clic en un producto para agregarlo</p>

          <div class="d-flex justify-content-between align-items-end my-3">
            <span class="text-muted">Total</span>
            <span class="total-venta">{{ dinero(total) }}</span>
          </div>

          <label class="form-label small mb-1" for="cliente">Cliente</label>
          <select id="cliente" v-model="idCliente" class="form-select mb-2">
            <option value="">Público en general</option>
            <option v-for="c in clientes" :key="c.id_cliente" :value="c.id_cliente">{{ c.nombre }}</option>
          </select>

          <label class="form-label small mb-1" for="metodo">Método de pago</label>
          <select id="metodo" v-model="metodo" class="form-select mb-2">
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
          </select>

          <div v-if="metodo === 'efectivo'" class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small mb-1" for="recibido">Recibido</label>
              <input id="recibido" v-model.number="recibido" type="number" min="0" class="form-control" placeholder="0.00">
            </div>
            <div class="col-6">
              <label class="form-label small mb-1">Cambio</label>
              <div class="form-control bg-light fw-semibold">{{ dinero(cambio) }}</div>
            </div>
          </div>

          <button class="btn btn-primary btn-lg w-100" :disabled="!carrito.length || guardando" @click="cobrar">
            <i class="bi bi-check2-circle me-1"></i> Cobrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { api } from '../services/api'
import { avisar } from '../services/avisos'
import { dinero } from '../services/formato'
import EtiquetaStock from '../components/EtiquetaStock.vue'

const productos = ref([])
const clientes = ref([])
const carrito = ref([])      // [{ id_producto, nombre, precio, stock, cantidad }]
const buscar = ref('')
const idCliente = ref('')
const metodo = ref('efectivo')
const recibido = ref('')
const guardando = ref(false)

// Valores calculados: se actualizan solos cuando cambia el carrito o lo recibido
const total = computed(() => carrito.value.reduce((suma, i) => suma + i.precio * i.cantidad, 0))
const cambio = computed(() => Math.max(Number(recibido.value) - total.value, 0))

onMounted(async () => {
  try {
    await cargarProductos()
    clientes.value = (await api('/clientes')).data
  } catch (e) {
    avisar(e.message, 'danger')
  }
})

async function cargarProductos() {
  productos.value = (await api('/productos?buscar=' + encodeURIComponent(buscar.value))).data
}

function agregar(producto) {
  const item = carrito.value.find(i => i.id_producto === producto.id_producto)
  if (!item) {
    carrito.value.push({ ...producto, precio: Number(producto.precio), cantidad: 1 })
  } else if (item.cantidad < producto.stock) {
    item.cantidad++
  } else {
    avisar(`Solo hay ${producto.stock} piezas de ${producto.nombre}.`, 'warning')
  }
}

function corregirCantidad(item) {
  item.cantidad = Math.min(Math.max(1, Math.floor(item.cantidad) || 1), item.stock)
}

function quitar(item) {
  carrito.value = carrito.value.filter(i => i !== item)
}

async function cobrar() {
  if (metodo.value === 'efectivo' && Number(recibido.value) < total.value) {
    return avisar('El dinero recibido es menor al total.', 'warning')
  }

  guardando.value = true
  try {
    const respuesta = await api('/ventas', 'POST', {
      id_cliente: idCliente.value,
      metodo_pago: metodo.value,
      productos: carrito.value.map(i => ({ id_producto: i.id_producto, cantidad: i.cantidad })),
    })

    let mensaje = `${respuesta.mensaje} Total: ${dinero(total.value)}`
    if (metodo.value === 'efectivo') mensaje += ` — Cambio: ${dinero(cambio.value)}`
    avisar(mensaje)

    // Limpiar para la siguiente venta
    carrito.value = []
    recibido.value = ''
    idCliente.value = ''
    await cargarProductos() // stock actualizado
  } catch (e) {
    avisar(e.message, 'danger')
  } finally {
    guardando.value = false
  }
}
</script>
