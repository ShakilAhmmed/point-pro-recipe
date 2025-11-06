import {ref} from 'vue'

export const authState = ref(!!localStorage.getItem('access_token'))

export const setToken = (token) => {
    localStorage.setItem('access_token', token)
    authState.value = true
}
export const getToken = () => localStorage.getItem('access_token')
export const clearToken = () => {
    localStorage.removeItem('access_token')
    authState.value = false
}
export const isAuthenticated = () => !!getToken()


