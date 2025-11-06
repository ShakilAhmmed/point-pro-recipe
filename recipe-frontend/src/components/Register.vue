<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-card">
        <h2 class="register-title">Register</h2>


        <Form :validation-schema="schema" @submit="onSubmit"  v-slot="{ isSubmitting, setErrors, setFieldError }" class="register-form">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <Field name="name" type="text" class="form-input"/>
            <ErrorMessage name="name" as="small" style="color:#ef4444"/>
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <Field name="email" type="email" class="form-input"/>
            <ErrorMessage name="email" as="small" style="color:#ef4444"/>
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <Field name="password" type="password" class="form-input"/>
            <ErrorMessage name="password" as="small" style="color:#ef4444"/>
          </div>

          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <Field name="password_confirmation" type="password" class="form-input"/>
            <ErrorMessage name="password_confirmation" as="small" style="color:#ef4444"/>
          </div>

          <button class="submit-button" :disabled="isSubmitting">
            {{ isSubmitting ? 'Creating account…' : 'Register' }}
          </button>
        </Form>

        <p class="redirect-text">
          Already have an account?
          <router-link to="/login" class="redirect-link">Login here</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import axiosRequest from '../helpers/axios.js'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const router = useRouter()
const serverError = ref('')

const schema = yup.object({
  name: yup.string().required('Name is required').min(2, 'At least 2 characters'),
  email: yup.string().required('Email is required').email('Enter a valid email'),
  password: yup.string().required('Password is required').min(8, 'Min 8 characters'),
  password_confirmation: yup
      .string()
      .required('Please confirm password')
      .oneOf([yup.ref('password')], 'Passwords do not match'),
})

const onSubmit = async (values, { setErrors, setFieldError }) => {
  serverError.value = ''
  try {
    await axiosRequest.post('/v1/auth/register', values)
    alert('Registration successful. Please log in.')
    router.push({ name: 'Login' })
  } catch (e) {
    const resp = e?.response
    if (!resp) {
      serverError.value = 'Network error. Check server/CORS.'
      return
    }
    if (resp.status === 422) {
      const errs = resp.data?.errors || resp.data?.data?.errors || {};
      const mapErrors = Object.fromEntries(
          Object.entries(errs).map(([k, v]) => [k, Array.isArray(v) ? v[0] : String(v)])
      )

      if (mapErrors.password && mapErrors.password.toLowerCase().includes('confirm')) {
        mapErrors.password_confirmation ??= mapErrors.password
      }
      setErrors(mapErrors)
      serverError.value = resp.data?.message || Object.values(mapErrors)[0] || 'Validation failed.'
      return
    }
    serverError.value = resp.data?.message || 'Registration failed.'
  }
}
</script>

<style scoped>
.register-page {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.register-container {
  width: 100%;
  max-width: 28rem;
  margin: 0 auto;
}

.register-card {
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.register-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 1.5rem;
  text-align: center;
}

.register-form {
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