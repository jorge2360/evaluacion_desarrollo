<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { pastelApi, ingredienteApi } from '../api/api.js'

const route = useRoute()
const router = useRouter()

const idPastel = route.params.id

const pastel = ref(null)
const ingredientesAsociados = ref([])
const ingredientesDisponibles = ref([])

const loading = ref(false)
const error = ref('')
const successMessage = ref('')
const selectedIngrediente = ref('')

const cargarDetalle = async () => {
  loading.value = true
  error.value = ''
  successMessage.value = ''

  try {
    const [pastelResponse, asociadosResponse, ingredientesResponse] = await Promise.all([
      pastelApi.getById(idPastel),
      pastelApi.getIngredientes(idPastel),
      ingredienteApi.getAll(),
    ])

    pastel.value = pastelResponse.data
    ingredientesAsociados.value = Array.isArray(asociadosResponse.data) ? asociadosResponse.data : []

    const todosIngredientes = Array.isArray(ingredientesResponse.data) ? ingredientesResponse.data : []

    const asociadosIds = ingredientesAsociados.value.map((item) => item.id_ingrediente)

    ingredientesDisponibles.value = todosIngredientes.filter(
      (ingrediente) => !asociadosIds.includes(ingrediente.id_ingrediente),
    )
  } catch (err) {
    error.value = err.message || 'Error al cargar el detalle del pastel'
  } finally {
    loading.value = false
  }
}

const asociarIngrediente = async () => {
  error.value = ''
  successMessage.value = ''

  if (!selectedIngrediente.value) {
    error.value = 'Debe seleccionar un ingrediente.'
    return
  }

  try {
    await pastelApi.asignarIngrediente(idPastel, {
      id_ingrediente: Number(selectedIngrediente.value),
    })

    successMessage.value = 'Ingrediente asociado correctamente.'
    selectedIngrediente.value = ''
    await cargarDetalle()
  } catch (err) {
    error.value = err.message || 'Error al asociar ingrediente'
  }
}

const desasociarIngrediente = async (idIngrediente) => {
  error.value = ''
  successMessage.value = ''

  const confirmado = window.confirm('¿Deseas desasociar este ingrediente del pastel?')
  if (!confirmado) return

  try {
    await pastelApi.desasociarIngrediente(idPastel, idIngrediente)
    successMessage.value = 'Ingrediente desasociado correctamente.'
    await cargarDetalle()
  } catch (err) {
    error.value = err.message || 'Error al desasociar ingrediente'
  }
}

const tituloPastel = computed(() => {
  return pastel.value ? pastel.value.nombre : 'Detalle del pastel'
})

onMounted(() => {
  cargarDetalle()
})
</script>

<template>
  <div>
    <button @click="router.push('/pasteles')" style="margin-bottom: 16px;">
      Volver a pasteles
    </button>

    <h1>{{ tituloPastel }}</h1>

    <p v-if="loading">Cargando detalle del pastel...</p>
    <p v-if="error">{{ error }}</p>
    <p v-if="successMessage">{{ successMessage }}</p>

    <div v-if="!loading && pastel">
      <h2>Información del pastel</h2>
      <p><strong>ID:</strong> {{ pastel.id_pastel }}</p>
      <p><strong>Nombre:</strong> {{ pastel.nombre }}</p>
      <p><strong>Descripción:</strong> {{ pastel.descripcion }}</p>
      <p><strong>Preparado por:</strong> {{ pastel.preparado_por }}</p>
      <p><strong>Fecha de creación:</strong> {{ pastel.fecha_creacion }}</p>
      <p><strong>Fecha de vencimiento:</strong> {{ pastel.fecha_vencimiento }}</p>

      <hr />

      <h2>Asociar ingrediente</h2>

      <div style="margin-bottom: 16px;">
        <select v-model="selectedIngrediente">
          <option value="">Seleccione un ingrediente</option>
          <option
            v-for="ingrediente in ingredientesDisponibles"
            :key="ingrediente.id_ingrediente"
            :value="ingrediente.id_ingrediente"
          >
            {{ ingrediente.nombre }}
          </option>
        </select>

        <button @click="asociarIngrediente" style="margin-left: 8px;">
          Asociar
        </button>
      </div>

      <hr />

      <h2>Ingredientes asociados</h2>

      <table
        v-if="ingredientesAsociados.length > 0"
        border="1"
        cellpadding="8"
        cellspacing="0"
      >
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha ingreso</th>
            <th>Fecha vencimiento</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="ingrediente in ingredientesAsociados"
            :key="ingrediente.id_ingrediente"
          >
            <td>{{ ingrediente.id_ingrediente }}</td>
            <td>{{ ingrediente.nombre }}</td>
            <td>{{ ingrediente.descripcion }}</td>
            <td>{{ ingrediente.fecha_ingreso }}</td>
            <td>{{ ingrediente.fecha_vencimiento }}</td>
            <td>
              <button @click="desasociarIngrediente(ingrediente.id_ingrediente)">
                Desasociar
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-else>Este pastel no tiene ingredientes asociados.</p>
    </div>
  </div>
</template>