<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card">
        <h2 class="login-title">Login</h2>


        <Form :validation-schema="schema" @submit="onSubmit" v-slot="{ errors, isSubmitting }" class="login-form">
          <div class="form-group">
            <label class="form-label">Email</label>
            <Field name="email" type="email" class="form-input"/>
            <ErrorMessage name="email" as="small" style="color:#ef4444"/>
            <span v-if="serverError" style="color:#ef4444; margin-bottom:.75rem;">  {{ serverError }}</span>
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <Field name="password" type="password" class="form-input"/>
            <ErrorMessage name="password" as="small" style="color:#ef4444"/>
          </div>

          <button class="submit-button" :disabled="isSubmitting">
            {{ isSubmitting ? 'Signing in…' : 'Login' }}
          </button>
        </Form>
      </div>
    </div>
  </div>
</template>

<script setup>
import {Form, Field, ErrorMessage, useForm} from 'vee-validate'
import * as yup from 'yup'
import axiosRequest from '../helpers/axios.js'
import {setToken} from '../helpers/auth.js'
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {extractServerMessage} from '../helpers/handleErrors.js'

const router = useRouter()
const serverError = ref('')

const schema = yup.object({
  email: yup.string().required('Email is required').email('Enter a valid email'),
  password: yup.string().required('Password is required').min(8, 'Min 8 characters'),
})

const {setErrors, setFieldError} = useForm()

const onSubmit = async (values) => {
  serverError.value = ''
  try {
    const {data} = await axiosRequest.post('/v1/auth/login', values)
    const token = data?.token?.access_token
    if (!token) throw new Error('No access token returned')
    setToken(token)
    router.push({name: 'Home'})
  } catch (e) {
    const resp = e?.response
    if (!resp) {
      serverError.value = 'Something went wrong.'
      return
    }

    if (resp.status === 422) {
      const errs = resp.data?.errors || {}
      // Field-level mapping
      const flattened = Object.fromEntries(
          Object.entries(errs).map(([k, v]) => [k, v?.[0] || 'Invalid value'])
      )
      setErrors(flattened)
      // Top banner = best backend message
      serverError.value = extractServerMessage(resp)
    } else if (resp.status === 401) {
      // For auth errors that are not field-specific
      serverError.value = resp.data?.message || 'Invalid credentials'
      // optionally pin to a specific field too:
      setFieldError('email', serverError.value)
    } else {
      serverError.value = extractServerMessage(resp)
    }
  }
}
</script>

<style scoped>
.login-page {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.login-container {
  width: 100%;
  max-width: 28rem;
  margin: 0 auto;
}

.login-card {
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.login-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 1.5rem;
  text-align: center;
}

.login-form {
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
  transition: all 0.2s;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.submit-button {
  width: 100%;
  background-color: #2563eb;
  color: white;
  padding: 0.625rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
}

.submit-button:hover {
  background-color: #1d4ed8;
}

.redirect-text {
  text-align: center;
  color: #4b5563;
  margin-top: 1rem;
}

.redirect-link {
  color: #2563eb;
  font-weight: 600;
  text-decoration: none;
}

.redirect-link:hover {
  text-decoration: underline;
}
</style>