<!-- Panel: resumen del negocio. Los datos vienen de GET /api/panel -->
<template>
  <div v-if="datos">
    <!-- Indicadores -->
    <div class="row g-3 mb-3">
      <div v-for="k in indicadores" :key="k.etiqueta" class="col-6 col-lg-3">
        <div class="card h-100">
          <div class="card-body kpi">
            <div class="icono" :class="k.color"><i class="bi" :class="k.icono"></i></div>
            <div>
              <div class="valor">{{ k.valor }}</div>
              <div class="etiqueta">{{ k.etiqueta }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">

      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header">Top 5 más vendidos <span class="badge bg-light text-muted fw-normal">JOIN</span></div>
          <ul class="list-group list-group-flush">
            <li v-for="(p, i) in datos.top" :key="p.nombre" class="list-group-item d-flex justify-content-between align-items-center gap-2">
              <span>{{ i + 1 }}. {{ p.nombre }}</span>
              <span class="badge bg-primary-subtle text-primary-emphasis text-nowrap">{{ p.unidades }} pzas</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Productos por agotarse</div>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead><tr><th>Producto</th><th class="text-center">Mínimo</th><th class="text-center">Stock</th></tr></thead>
              <tbody>
                <tr v-for="p in datos.stock_bajo" :key="p.codigo">
                  <td>{{ p.nombre }}</td>
                  <td class="text-center">{{ p.stock_minimo }}</td>
                  <td class="text-center"><EtiquetaStock :stock="p.stock" :minimo="p.stock_minimo" /></td>
                </tr>
                <tr v-if="!datos.stock_bajo.length"><td colspan="3" class="vacio">Todo en orden</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header">Productos que nunca se han vendido <span class="badge bg-light text-muted fw-normal">Subconsulta</span></div>
          <div class="table-responsive" style="max-height: 300px">
            <table class="table table-sm mb-0">
              <thead><tr><th>Código</th><th>Producto</th><th class="text-center">Stock</th></tr></thead>
              <tbody>
                <tr v-for="p in datos.sin_ventas" :key="p.codigo">
                  <td class="small text-nowrap">{{ p.codigo }}</td>
                  <td>{{ p.nombre }}</td>
                  <td class="text-center">{{ p.stock }}</td>
                </tr>
              </tbody>
            </table>
          </div>
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

const datos = ref(null)

// computed: se recalcula solo cuando cambian los datos
const indicadores = computed(() => [
  { etiqueta: 'Vendido hoy',      valor: dinero(datos.value.hoy.total),  icono: 'bi-cash-stack',          color: 'bg-suave-verde' },
  { etiqueta: 'Ventas hoy',       valor: datos.value.hoy.num_ventas,     icono: 'bi-receipt',             color: 'bg-suave-azul' },
  { etiqueta: 'Con stock bajo',   valor: datos.value.stock_bajo.length,  icono: 'bi-exclamation-triangle', color: 'bg-suave-rojo' },
  { etiqueta: 'Sin vender nunca', valor: datos.value.sin_ventas.length,  icono: 'bi-box-seam',            color: 'bg-suave-amarillo' },
])

onMounted(async () => {
  try {
    datos.value = (await api('/panel')).data
  } catch (e) {
    avisar(e.message, 'danger')
  }
})
</script>
