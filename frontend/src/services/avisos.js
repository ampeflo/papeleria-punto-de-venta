/**
 * Mensajes flotantes (toasts). Cualquier vista llama a avisar('texto').
 * El componente Avisos.vue se encarga de mostrarlos.
 */
import { reactive } from 'vue'

export const avisos = reactive([])
let contador = 0

/** tipo: 'success' (verde), 'danger' (rojo) o 'warning' (amarillo) */
export function avisar(mensaje, tipo = 'success') {
  const id = ++contador
  avisos.push({ id, mensaje, tipo })
  setTimeout(() => cerrarAviso(id), 4000)
}

export function cerrarAviso(id) {
  const i = avisos.findIndex(a => a.id === id)
  if (i !== -1) avisos.splice(i, 1)
}
