<template>
  <div class="login-fondo">
    <div class="card login-card shadow-lg">
      <div class="card-body p-4 p-sm-5">
        <div class="text-center mb-4">
          <div class="logo mb-3"><i class="bi bi-pencil-fill"></i></div>
          <h1 class="h4 fw-bold mb-1">El Lápiz Feliz</h1>
          <p class="text-muted small mb-0">Punto de venta</p>
        </div>

        <div v-if="error" class="alert alert-danger py-2 small">{{ error }}</div>

        <form ref="formulario" :class="{ 'was-validated': validado }" novalidate @submit.prevent="entrar">
          <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input id="usuario" v-model.trim="usuario" class="form-control" required autofocus>
            <div class="invalid-feedback">Escribe tu usuario.</div>
          </div>
          <div class="mb-4">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" v-model="password" type="password" class="form-control" required>
            <div class="invalid-feedback">Escribe tu contraseña.</div>
          </div>
          <button class="btn btn-primary w-100 py-2" :disabled="cargando">
            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { iniciarSesion } from '../services/sesion'

const router = useRouter()
const usuario = ref('')
const password = ref('')
const error = ref('')
const validado = ref(false)
const cargando = ref(false)
const formulario = ref(null)

async function entrar() {
  validado.value = true
  if (!formulario.value.checkValidity()) return

  cargando.value = true
  error.value = ''
  try {
    await iniciarSesion(usuario.value, password.value)
    router.push('/panel')
  } catch (e) {
    error.value = e.message
  } finally {
    cargando.value = false
  }
}
</script>
