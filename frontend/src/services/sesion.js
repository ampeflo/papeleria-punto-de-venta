/**
 * Estado de la sesión, compartido por toda la aplicación.
 * reactive() hace que la interfaz se actualice sola cuando cambia el usuario.
 */
import { reactive, computed } from 'vue'
import { api } from './api'

export const sesion = reactive({
  usuario: null, // { id_usuario, nombre, usuario, rol }
})

export const esAdmin = computed(() => sesion.usuario?.rol === 'Administrador')

/** Pregunta al backend si ya hay una sesión iniciada. */
export async function cargarSesion() {
  try {
    sesion.usuario = (await api('/sesion')).data
  } catch {
    sesion.usuario = null
  }
}

export async function iniciarSesion(usuario, password) {
  sesion.usuario = (await api('/login', 'POST', { usuario, password })).data
}

export async function cerrarSesion() {
  await api('/logout', 'POST')
  sesion.usuario = null
}
