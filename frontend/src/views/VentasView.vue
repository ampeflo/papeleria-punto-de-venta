<!-- Historial de ventas con filtro por fechas y ticket de cada venta -->
<template>
  <div>
    <div class="card mb-3">
      <div class="card-body row g-2 align-items-end">
        <div class="col-6 col-md-3">
          <label class="form-label small mb-1" for="desde">Desde</label>
          <input id="desde" v-model="desde" type="date" class="form-control">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label small mb-1" for="hasta">Hasta</label>
          <input id="hasta" v-model="hasta" type="date" class="form-control">
        </div>
        <div class="col-12 col-md-3">
          <button class="btn btn-primary w-100" @click="cargar"><i class="bi bi-funnel"></i> Filtrar</button>
        </div>
        <div class="col-12 col-md-3 text-md-end">
          <div class="small text-muted">Total del periodo</div>
          <div class="fs-4 fw-bold text-primary">{{ dinero(totalPeriodo) }}</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Folio</th><th>Fecha</th><th>Cliente</th><th>Cajero</th><th>Pago</th><th class="text-end">Total</th><th></th></tr></thead>
          <tbody>
            <tr v-for="v in ventas" :key="v.id_venta">
              <td class="fw-semibold">#{{ v.id_venta }}</td>
              <td class="small">{{ fecha(v.fecha) }}</td>
              <td>{{ v.cliente }}</td>
              <td>{{ v.cajero }}</td>
              <td class="text-capitalize">{{ v.metodo_pago }}</td>
              <td class="text-end fw-semibold">{{ dinero(v.total) }}</td>
              <td class="acciones">
                <button class="btn btn-sm btn-outline-primary" title="Ver ticket" @click="verTicket(v.id_venta)">
                  <i class="bi bi-eye"></i>
                </button>
              </td>
            </tr>
            <tr v-if="!ventas.length"><td colspan="7" class="vacio">No hay ventas en estas fechas</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Ticket -->
    <VentanaModal ref="modal" titulo="Ticket de venta">
      <div class="modal-body">
        <div v-if="ticket" class="ticket">
          <div class="text-center fw-bold">PAPELERÍA EL LÁPIZ FELIZ</div>
          <hr>
          <div>Folio: #{{ ticket.id_venta }}</div>
          <div>Fecha: {{ fecha(ticket.fecha) }}</div>
          <div>Cajero: {{ ticket.cajero }}</div>
          <div>Cliente: {{ ticket.cliente }}</div>
          <hr>
          <div v-for="p in ticket.productos" :key="p.nombre" class="d-flex justify-content-between">
            <span>{{ p.cantidad }} x {{ p.nombre }}</span><span>{{ dinero(p.importe) }}</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fw-bold"><span>TOTAL</span><span>{{ dinero(ticket.total) }}</span></div>
          <div class="d-flex justify-content-between"><span>Pago</span><span class="text-capitalize">{{ ticket.metodo_pago }}</span></div>
          <hr>
          <div class="text-center">¡Gracias por su compra!</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="imprimir"><i class="bi bi-printer me-1"></i> Imprimir</button>
      </div>
    </VentanaModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { api } from '../services/api'
import { avisar } from '../services/avisos'
import { dinero, fecha, fechaISO } from '../services/formato'
import VentanaModal from '../components/VentanaModal.vue'

const ventas = ref([])
const desde = ref(fechaISO(7))   // hace 7 días
const hasta = ref(fechaISO())    // hoy
const ticket = ref(null)
const modal = ref(null)

const totalPeriodo = computed(() => ventas.value.reduce((suma, v) => suma + Number(v.total), 0))

onMounted(cargar)

async function cargar() {
  try {
    ventas.value = (await api(`/ventas?desde=${desde.value}&hasta=${hasta.value}`)).data
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

async function verTicket(id) {
  try {
    ticket.value = (await api('/ventas/' + id)).data
    modal.value.abrir()
  } catch (e) {
    avisar(e.message, 'danger')
  }
}

function imprimir() {
  window.print()
}
</script>
