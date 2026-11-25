/** Formatos para mostrar datos. */

export function dinero(n) {
  return Number(n || 0).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })
}

export function fecha(texto) {
  return new Date(texto.replace(' ', 'T')).toLocaleString('es-MX', { dateStyle: 'medium', timeStyle: 'short' })
}

/** Fecha en formato AAAA-MM-DD (para los <input type="date">). */
export function fechaISO(diasAtras = 0) {
  const f = new Date()
  f.setDate(f.getDate() - diasAtras)
  return f.toLocaleDateString('en-CA')
}
