<template>
  <div>
    <nav>
      <router-link to="/">Inicio</router-link> |
      <router-link to="/ingredientes">Ingredientes</router-link> |
      <router-link to="/pasteles">Pasteles</router-link> |
      <router-link to="/reporte">Reporte</router-link>
    </nav>

    <hr />

    <router-view />
  </div>
</template>