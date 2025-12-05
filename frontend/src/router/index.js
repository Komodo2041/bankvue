// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

import Home from '../components/Home.vue'
import ImportView from '../components/ImportView.vue'

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/import/:id',       // parametr dynamiczny :id
        name: 'ImportView',
        component: ImportView,
        props: true                 // automatycznie przekazuje parametry jako props
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router