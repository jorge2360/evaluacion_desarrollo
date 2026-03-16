<template>
  <div class="container">
    <div class="card">
      <h1>Sistema de gestión de pasteles</h1>

      <nav class="navbar">
        <router-link to="/">Inicio</router-link>
        <router-link to="/ingredientes">Ingredientes</router-link>
        <router-link to="/pasteles">Pasteles</router-link>
        <router-link to="/reporte">Reporte</router-link>
      </nav>
    </div>

    <router-view />
  </div>
</template>