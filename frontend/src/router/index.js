/**
 * Rutas del frontend: qué vista se muestra en cada dirección.
 * meta.titulo y meta.icono se usan para dibujar el menú lateral.
 */
import { createRouter, createWebHashHistory } from 'vue-router'
import { sesion, cargarSesion } from '../services/sesion'

import LoginView from '../views/LoginView.vue'
import PanelView from '../views/PanelView.vue'
import VentaView from '../views/VentaView.vue'
import VentasView from '../views/VentasView.vue'
import ProductosView from '../views/ProductosView.vue'
import CategoriasView from '../views/CategoriasView.vue'
import ClientesView from '../views/ClientesView.vue'

export const rutas = [
  { path: '/login',      component: LoginView,      meta: { publica: true } },
  { path: '/panel',      component: PanelView,      meta: { titulo: 'Panel',       icono: 'bi-speedometer2' } },
  { path: '/venta',      component: VentaView,      meta: { titulo: 'Nueva venta', icono: 'bi-cart-plus' } },
  { path: '/ventas',     component: VentasView,     meta: { titulo: 'Ventas',      icono: 'bi-receipt' } },
  { path: '/productos',  component: ProductosView,  meta: { titulo: 'Productos',   icono: 'bi-box-seam' } },
  { path: '/categorias', component: CategoriasView, meta: { titulo: 'Categorías',  icono: 'bi-tags' } },
  { path: '/clientes',   component: ClientesView,   meta: { titulo: 'Clientes',    icono: 'bi-people' } },
  { path: '/:pathMatch(.*)*', redirect: '/panel' },
]

const router = createRouter({
  // Hash (#/panel): funciona en Apache sin configuración extra
  history: createWebHashHistory(),
  routes: rutas,
})

/**
 * Guardia de navegación: antes de entrar a una página privada
 * revisa que haya sesión; si no, manda al login.
 */
router.beforeEach(async (destino) => {
  if (destino.meta.publica) return true
  if (!sesion.usuario) await cargarSesion()
  if (!sesion.usuario) return '/login'
})

export default router
