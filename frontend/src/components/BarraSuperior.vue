<!-- Barra superior: título de la página, usuario y botón para salir -->
<template>
  <header class="topbar">
    <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Menú">
      <i class="bi bi-list fs-5"></i>
    </button>

    <h1>{{ $route.meta.titulo }}</h1>

    <div class="ms-auto d-flex align-items-center gap-2">
      <div class="text-end lh-sm d-none d-sm-block">
        <div class="fw-semibold small">{{ sesion.usuario.nombre }}</div>
        <div class="text-muted" style="font-size: .75rem">{{ sesion.usuario.rol }}</div>
      </div>
      <button class="btn btn-outline-danger btn-sm" title="Cerrar sesión" @click="salir">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { sesion, cerrarSesion } from '../services/sesion'

const router = useRouter()

async function salir() {
  await cerrarSesion()
  router.push('/login')
}
</script>
