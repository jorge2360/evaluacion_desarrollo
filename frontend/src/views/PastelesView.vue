<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { pastelApi } from '../api/api.js'

const router = useRouter()

const pasteles = ref([])
const loading = ref(false)
const saving = ref(false)
const error = ref('')
const successMessage = ref('')
const editingId = ref(null)

const form = ref({
  nombre: '',
  descripcion: '',
  preparado_por: '',
  fecha_creacion: '',
  fecha_vencimiento: '',
})

const resetForm = () => {
  form.value = {
    nombre: '',
    descripcion: '',
    preparado_por: '',
    fecha_creacion: '',
    fecha_vencimiento: '',
  }
  editingId.value = null
}

const cargarPasteles = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await pastelApi.getAll()
    pasteles.value = Array.isArray(response.data) ? response.data : []
  } catch (err) {
    error.value = err.message || 'Error al cargar pasteles'
  } finally {
    loading.value = false
  }
}

const guardarPastel = async () => {
  error.value = ''
  successMessage.value = ''

  if (
    !form.value.nombre.trim() ||
    !form.value.preparado_por.trim() ||
    !form.value.fecha_creacion ||
    !form.value.fecha_vencimiento
  ) {
    error.value =
      'Los campos nombre, preparado por, fecha de creación y fecha de vencimiento son obligatorios.'
    return
  }

  try {
    saving.value = true

    const payload = {
      nombre: form.value.nombre,
      descripcion: form.value.descripcion,
      preparado_por: form.value.preparado_por,
      fecha_creacion: form.value.fecha_creacion,
      fecha_vencimiento: form.value.fecha_vencimiento,
    }

    if (editingId.value) {
      await pastelApi.update(editingId.value, payload)
      successMessage.value = 'Pastel actualizado correctamente.'
    } else {
      await pastelApi.create(payload)
      successMessage.value = 'Pastel creado correctamente.'
    }

    resetForm()
    await cargarPasteles()
  } catch (err) {
    error.value = err.message || 'Error al guardar pastel'
  } finally {
    saving.value = false
  }
}

const editarPastel = (pastel) => {
  error.value = ''
  successMessage.value = ''
  editingId.value = pastel.id_pastel

  form.value = {
    nombre: pastel.nombre || '',
    descripcion: pastel.descripcion || '',
    preparado_por: pastel.preparado_por || '',
    fecha_creacion: pastel.fecha_creacion || '',
    fecha_vencimiento: pastel.fecha_vencimiento || '',
  }
}

const eliminarPastel = async (id) => {
  error.value = ''
  successMessage.value = ''

  const confirmado = window.confirm('¿Deseas eliminar este pastel?')
  if (!confirmado) return

  try {
    await pastelApi.delete(id)
    successMessage.value = 'Pastel eliminado correctamente.'

    if (editingId.value === id) {
      resetForm()
    }

    await cargarPasteles()
  } catch (err) {
    error.value = err.message || 'Error al eliminar pastel'
  }
}

const verDetalle = (id) => {
  router.push(`/pasteles/${id}`)
}

onMounted(() => {
  cargarPasteles()
})
</script>

<template>
  <div>
    <h1>Pasteles</h1>

    <h2>{{ editingId ? 'Editar pastel' : 'Nuevo pastel' }}</h2>

    <form @submit.prevent="guardarPastel" style="margin-bottom: 16px;">
      <div style="margin-bottom: 8px;">
        <label for="nombre">Nombre</label><br />
        <input id="nombre" v-model="form.nombre" type="text" />
      </div>

      <div style="margin-bottom: 8px;">
        <label for="descripcion">Descripción</label><br />
        <textarea id="descripcion" v-model="form.descripcion"></textarea>
      </div>

      <div style="margin-bottom: 8px;">
        <label for="preparado_por">Preparado por</label><br />
        <input id="preparado_por" v-model="form.preparado_por" type="text" />
      </div>

      <div style="margin-bottom: 8px;">
        <label for="fecha_creacion">Fecha de creación</label><br />
        <input id="fecha_creacion" v-model="form.fecha_creacion" type="date" />
      </div>

      <div style="margin-bottom: 8px;">
        <label for="fecha_vencimiento">Fecha de vencimiento</label><br />
        <input id="fecha_vencimiento" v-model="form.fecha_vencimiento" type="date" />
      </div>

      <button type="submit" :disabled="saving">
        {{ saving ? 'Guardando...' : editingId ? 'Actualizar pastel' : 'Guardar pastel' }}
      </button>

      <button
        v-if="editingId"
        type="button"
        @click="resetForm"
        style="margin-left: 8px;"
      >
        Cancelar edición
      </button>
    </form>

    <p v-if="error">{{ error }}</p>
    <p v-if="successMessage">{{ successMessage }}</p>

    <hr />

    <h2>Listado de pasteles</h2>

    <p v-if="loading">Cargando pasteles...</p>

    <div v-else>
      <table v-if="pasteles.length > 0" border="1" cellpadding="8" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Preparado por</th>
            <th>Fecha creación</th>
            <th>Fecha vencimiento</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pastel in pasteles" :key="pastel.id_pastel">
            <td>{{ pastel.id_pastel }}</td>
            <td>{{ pastel.nombre }}</td>
            <td>{{ pastel.descripcion }}</td>
            <td>{{ pastel.preparado_por }}</td>
            <td>{{ pastel.fecha_creacion }}</td>
            <td>{{ pastel.fecha_vencimiento }}</td>
            <td style="min-width: 220px;">
            <div class="actions">
                <button type="button" @click="verDetalle(pastel.id_pastel)">Detalle</button>
                <button type="button" class="button-warning" @click="editarPastel(pastel)">Editar</button>
                <button type="button" class="button-danger" @click="eliminarPastel(pastel.id_pastel)">Eliminar</button>
            </div>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-else>No hay pasteles registrados.</p>
    </div>
  </div>
</template>