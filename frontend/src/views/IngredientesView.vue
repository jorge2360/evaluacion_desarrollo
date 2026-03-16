<script setup>
import { ref, onMounted } from 'vue'
import { ingredienteApi } from '../api/api.js'

const ingredientes = ref([])
const loading = ref(false)
const saving = ref(false)
const error = ref('')
const successMessage = ref('')
const editingId = ref(null)

const form = ref({
  nombre: '',
  descripcion: '',
  fecha_ingreso: '',
  fecha_vencimiento: '',
})

const resetForm = () => {
  form.value = {
    nombre: '',
    descripcion: '',
    fecha_ingreso: '',
    fecha_vencimiento: '',
  }
  editingId.value = null
}

const cargarIngredientes = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await ingredienteApi.getAll()
    ingredientes.value = Array.isArray(response.data) ? response.data : []
  } catch (err) {
    error.value = err.message || 'Error al cargar ingredientes'
  } finally {
    loading.value = false
  }
}

const guardarIngrediente = async () => {
  error.value = ''
  successMessage.value = ''

  if (
    !form.value.nombre.trim() ||
    !form.value.fecha_ingreso ||
    !form.value.fecha_vencimiento
  ) {
    error.value = 'Los campos nombre, fecha de ingreso y fecha de vencimiento son obligatorios.'
    return
  }

  try {
    saving.value = true

    const payload = {
      nombre: form.value.nombre,
      descripcion: form.value.descripcion,
      fecha_ingreso: form.value.fecha_ingreso,
      fecha_vencimiento: form.value.fecha_vencimiento,
    }

    if (editingId.value) {
      await ingredienteApi.update(editingId.value, payload)
      successMessage.value = 'Ingrediente actualizado correctamente.'
    } else {
      await ingredienteApi.create(payload)
      successMessage.value = 'Ingrediente creado correctamente.'
    }

    resetForm()
    await cargarIngredientes()
  } catch (err) {
    error.value = err.message || 'Error al guardar ingrediente'
  } finally {
    saving.value = false
  }
}

const editarIngrediente = (ingrediente) => {
  error.value = ''
  successMessage.value = ''
  editingId.value = ingrediente.id_ingrediente

  form.value = {
    nombre: ingrediente.nombre || '',
    descripcion: ingrediente.descripcion || '',
    fecha_ingreso: ingrediente.fecha_ingreso || '',
    fecha_vencimiento: ingrediente.fecha_vencimiento || '',
  }
}

const eliminarIngrediente = async (id) => {
  error.value = ''
  successMessage.value = ''

  const confirmado = window.confirm('¿Deseas eliminar este ingrediente?')
  if (!confirmado) return

  try {
    await ingredienteApi.delete(id)
    successMessage.value = 'Ingrediente eliminado correctamente.'

    if (editingId.value === id) {
      resetForm()
    }

    await cargarIngredientes()
  } catch (err) {
    error.value = err.message || 'Error al eliminar ingrediente'
  }
}

onMounted(() => {
  cargarIngredientes()
})
</script>

<template>
  <div>
    <h1>Ingredientes</h1>

    <h2>{{ editingId ? 'Editar ingrediente' : 'Nuevo ingrediente' }}</h2>

    <form @submit.prevent="guardarIngrediente" style="margin-bottom: 16px;">
      <div style="margin-bottom: 8px;">
        <label for="nombre">Nombre</label><br />
        <input id="nombre" v-model="form.nombre" type="text" />
      </div>

      <div style="margin-bottom: 8px;">
        <label for="descripcion">Descripción</label><br />
        <textarea id="descripcion" v-model="form.descripcion"></textarea>
      </div>

      <div style="margin-bottom: 8px;">
        <label for="fecha_ingreso">Fecha de ingreso</label><br />
        <input id="fecha_ingreso" v-model="form.fecha_ingreso" type="date" />
      </div>

      <div style="margin-bottom: 8px;">
        <label for="fecha_vencimiento">Fecha de vencimiento</label><br />
        <input id="fecha_vencimiento" v-model="form.fecha_vencimiento" type="date" />
      </div>

      <button type="submit" :disabled="saving">
        {{ saving ? 'Guardando...' : editingId ? 'Actualizar ingrediente' : 'Guardar ingrediente' }}
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

    <h2>Listado de ingredientes</h2>

    <p v-if="loading">Cargando ingredientes...</p>

    <div v-else>
      <table v-if="ingredientes.length > 0" border="1" cellpadding="8" cellspacing="0">
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
          <tr v-for="ingrediente in ingredientes" :key="ingrediente.id_ingrediente">
            <td>{{ ingrediente.id_ingrediente }}</td>
            <td>{{ ingrediente.nombre }}</td>
            <td>{{ ingrediente.descripcion }}</td>
            <td>{{ ingrediente.fecha_ingreso }}</td>
            <td>{{ ingrediente.fecha_vencimiento }}</td>
            <td>
              <button @click="editarIngrediente(ingrediente)">Editar</button>
              <button
                @click="eliminarIngrediente(ingrediente.id_ingrediente)"
                style="margin-left: 8px;"
              >
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-else>No hay ingredientes registrados.</p>
    </div>
  </div>
</template>