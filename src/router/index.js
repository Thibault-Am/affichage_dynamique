import { createRouter, createWebHistory } from 'vue-router'
import Screens from '../views/Screens.vue'

const routes = [{
        path: '/',
        name: 'Screens',
        component: Screens,
    },
    {
        path: '/TokenSelect',
        name: 'TokenSelect',
        component: function() {
            return import ( /* webpackChunkName: "about" */ '../views/TokenSelect.vue')
        }
    }
]

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes
})

export default router