import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import IngredientesView from '../views/IngredientesView.vue'
import PastelesView from '../views/PastelesView.vue'
import PastelDetalleView from '../views/PastelDetalleView.vue'
import ReporteView from '../views/ReporteView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/ingredientes',
      name: 'ingredientes',
      component: IngredientesView,
    },
    {
      path: '/pasteles',
      name: 'pasteles',
      component: PastelesView,
    },
    {
      path: '/pasteles/:id',
      name: 'pastel-detalle',
      component: PastelDetalleView,
    },
    {
      path: '/reporte',
      name: 'reporte',
      component: ReporteView,
    },
  ],
})

export default router