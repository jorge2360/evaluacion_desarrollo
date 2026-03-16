const API_BASE_URL = 'http://localhost:8000'

async function request(endpoint, options = {}) {
  const headers = {
    ...(options.headers || {}),
  }

  if (options.body) {
    headers['Content-Type'] = 'application/json'
  }

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers,
  })

  const data = await response.json()

  if (!response.ok) {
    throw new Error(data.message || 'Error en la solicitud')
  }

  return data
}

export const ingredienteApi = {
  getAll() {
    return request('/ingredientes')
  },
  getById(id) {
    return request(`/ingredientes/${id}`)
  },
  create(payload) {
    return request('/ingredientes', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  update(id, payload) {
    return request(`/ingredientes/${id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  delete(id) {
    return request(`/ingredientes/${id}`, {
      method: 'DELETE',
    })
  },
}

export const pastelApi = {
  getAll() {
    return request('/pasteles')
  },
  getById(id) {
    return request(`/pasteles/${id}`)
  },
  create(payload) {
    return request('/pasteles', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  update(id, payload) {
    return request(`/pasteles/${id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  delete(id) {
    return request(`/pasteles/${id}`, {
      method: 'DELETE',
    })
  },
  getIngredientes(idPastel) {
    return request(`/pasteles/${idPastel}/ingredientes`)
  },
  asignarIngrediente(idPastel, payload) {
    return request(`/pasteles/${idPastel}/ingredientes`, {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  desasociarIngrediente(idPastel, idIngrediente) {
    return request(`/pasteles/${idPastel}/ingredientes/${idIngrediente}`, {
      method: 'DELETE',
    })
  },
}

export const reporteApi = {
  getPastelesConIngredientes() {
    return request('/reporte/pasteles-ingredientes')
  },
}