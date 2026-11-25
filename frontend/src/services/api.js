/**
 * Comunicación con el backend (API PHP).
 *
 *   const respuesta = await api('/productos')                     // GET
 *   await api('/productos', 'POST', { nombre: 'Lápiz', ... })      // crear
 *   await api('/productos/5', 'PUT', { ... })                      // editar
 *   await api('/productos/5', 'DELETE')                            // eliminar
 */

// En desarrollo es "/api" (lo reenvía el proxy de Vite).
// Al compilar se toma de .env.production
const URL_API = import.meta.env.VITE_API_URL || '/api'

export async function api(ruta, metodo = 'GET', datos = null) {
  let respuesta
  try {
    respuesta = await fetch(URL_API + ruta, {
      method: metodo,
      headers: { 'Content-Type': 'application/json' },
      body: datos ? JSON.stringify(datos) : null,
    })
  } catch {
    throw new Error('No hay conexión con el servidor. ¿Está encendido WampServer?')
  }

  const json = await respuesta.json()
  if (!json.ok) {
    throw new Error(json.mensaje)
  }
  return json // { ok, data, mensaje }
}
