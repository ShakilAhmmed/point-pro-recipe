import {createRouter, createWebHistory} from 'vue-router'
import {isAuthenticated} from '../helpers/auth.js'

import Home from "../components/Home.vue"
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'

const routes = [
    {path: '/', redirect: '/login'},
    {path: '/login', name: 'Login', component: Login, meta: {guestOnly: true}},
    {path: '/register', name: 'Register', component: Register, meta: {guestOnly: true}},
    {path: '/home', name: 'Home', component: Home, meta: {requiresAuth: true}},
    {path: '/:pathMatch(.*)*', redirect: '/login'},
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
})

router.beforeEach((to) => {
    const loggedIn = isAuthenticated()
    if (to.meta.requiresAuth && !loggedIn) return {name: 'Login'}
    if (to.meta.guestOnly && loggedIn) return {name: 'Home'}
    return true
})

export default router
