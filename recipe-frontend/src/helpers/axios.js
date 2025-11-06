import axios from 'axios'
import {getToken} from './auth.js'

const axiosRequest = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000/api',
    timeout: 15000,
})

// Attach token
axiosRequest.interceptors.request.use((config) => {
    const token = getToken()
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
})

// Handle 401 → login
axiosRequest.interceptors.response.use(
    (r) => r,
    (error) => {
        if (error?.response?.status === 401) {
            localStorage.removeItem('access_token')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

export default axiosRequest
