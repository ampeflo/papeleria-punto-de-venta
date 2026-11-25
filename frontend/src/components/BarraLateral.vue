<!-- Menú lateral. En celular se convierte en un panel que se abre con el botón ☰ -->
<template>
  <aside id="sidebar" ref="el" class="sidebar offcanvas-lg offcanvas-start" tabindex="-1">
    <div class="offcanvas-header d-lg-none pb-0">
      <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
    </div>

    <router-link to="/panel" class="marca">
      <span class="logo"><i class="bi bi-pencil-fill"></i></span>El Lápiz Feliz
    </router-link>

    <nav class="nav flex-column">
      <router-link v-for="ruta in menu" :key="ruta.path" :to="ruta.path" class="nav-link" active-class="active">
        <i class="bi" :class="ruta.meta.icono"></i>{{ ruta.meta.titulo }}
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Offcanvas } from 'bootstrap'
import { rutas } from '../router'

// El menú se arma con las rutas que tienen título
const menu = rutas.filter(r => r.meta?.titulo)

// En celular, cerrar el menú al cambiar de página
const el = ref(null)
const route = useRoute()
watch(() => route.path, () => Offcanvas.getInstance(el.value)?.hide())
</script>
