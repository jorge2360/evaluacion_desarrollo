<script setup>
import { ref, onMounted } from 'vue'
import { reporteApi } from '../api/api.js'

const reporte = ref([])
const loading = ref(false)
const error = ref('')

const cargarReporte = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await reporteApi.getPastelesConIngredientes()
    reporte.value = Array.isArray(response.data) ? response.data : []
  } catch (err) {
    error.value = err.message || 'Error al cargar el reporte'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  cargarReporte()
})
</script>

<template>
  <div>
    <h1>Reporte de pasteles e ingredientes</h1>

    <p v-if="loading">Cargando reporte...</p>
    <p v-else-if="error">{{ error }}</p>

    <div v-else>
      <div v-if="reporte.length > 0">
        <div
          v-for="pastel in reporte"
          :key="pastel.id_pastel"
          style="border: 1px solid #ccc; padding: 16px; margin-bottom: 16px;"
        >
          <h2>{{ pastel.nombre }}</h2>
          <p><strong>ID:</strong> {{ pastel.id_pastel }}</p>
          <p><strong>Descripción:</strong> {{ pastel.descripcion }}</p>
          <p><strong>Preparado por:</strong> {{ pastel.preparado_por }}</p>
          <p><strong>Fecha de creación:</strong> {{ pastel.fecha_creacion }}</p>
          <p><strong>Fecha de vencimiento:</strong> {{ pastel.fecha_vencimiento }}</p>

          <h3>Ingredientes</h3>

          <table
            v-if="pastel.ingredientes && pastel.ingredientes.length > 0"
            border="1"
            cellpadding="8"
            cellspacing="0"
            style="width: 100%;"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha ingreso</th>
                <th>Fecha vencimiento</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="ingrediente in pastel.ingredientes"
                :key="ingrediente.id_pastel_ingrediente"
              >
                <td>{{ ingrediente.id_ingrediente }}</td>
                <td>{{ ingrediente.nombre }}</td>
                <td>{{ ingrediente.descripcion }}</td>
                <td>{{ ingrediente.fecha_ingreso }}</td>
                <td>{{ ingrediente.fecha_vencimiento }}</td>
              </tr>
            </tbody>
          </table>

          <p v-else>Este pastel no tiene ingredientes asociados.</p>
        </div>
      </div>

      <p v-else>No hay información para mostrar en el reporte.</p>
    </div>
  </div>
</template>