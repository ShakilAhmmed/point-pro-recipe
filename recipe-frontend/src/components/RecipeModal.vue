<template>
  <div v-if="modelValue" class="modal-overlay" @click="emitClose">
    <div class="modal-container" @click.stop>
      <div class="modal-header">
        <h2 class="modal-title">Create New Recipe</h2>
        <button class="close-button" @click="emitClose">✕</button>
      </div>

      <div class="modal-body">
        <Form
            :validation-schema="schema"
            :initial-values="initialValues"
            @submit="onSubmit"
            v-slot="{ isSubmitting, setFieldValue, resetForm }"
        >
          <!-- Top row -->
          <div class="form-row">
            <div class="form-group">
              <label for="recipe-name">Recipe Name</label>
              <Field id="recipe-name" name="name" type="text" class="form-input" placeholder="Enter recipe name"/>
              <ErrorMessage name="name" as="small" class="err"/>
            </div>

            <div class="form-group">
              <label for="cuisine-type">Cuisine Type</label>
              <Field id="cuisine-type" name="cuisine_type" type="text" class="form-input" placeholder="e.g., Italian"/>
              <ErrorMessage name="cuisine_type" as="small" class="err"/>
            </div>

            <div class="form-group">
              <label for="image-upload">Image Upload</label>
              <input
                  id="image-upload"
                  type="file"
                  accept="image/*"
                  class="form-input-file"
                  @change="(e)=>onImageChange(e, setFieldValue)"
              />
              <small v-if="fileError" class="err">{{ fileError }}</small>
              <div v-if="imagePreview" class="image-preview">
                <img :src="imagePreview" alt="Preview"/>
              </div>
            </div>
          </div>

          <!-- Ingredients -->
          <div class="section-title">Ingredients</div>
          <div class="table-container">
            <table class="ingredients-table">
              <thead>
              <tr><th>Name</th><th>Quantity</th><th>Unit</th><th></th></tr>
              </thead>
              <tbody>
              <FieldArray name="ingredients" v-slot="{ fields, push, remove }">
                <tr v-for="(row, i) in fields" :key="row.key">
                  <td>
                    <Field :name="`ingredients[${i}].name`" class="table-input" placeholder="Ingredient name"/>
                    <ErrorMessage :name="`ingredients[${i}].name`" as="small" class="err"/>
                  </td>
                  <td>
                    <Field :name="`ingredients[${i}].quantity`" class="table-input" placeholder="1.25"/>
                    <ErrorMessage :name="`ingredients[${i}].quantity`" as="small" class="err"/>
                  </td>
                  <td>
                    <Field :name="`ingredients[${i}].unit`" class="table-input" placeholder="kg"/>
                    <ErrorMessage :name="`ingredients[${i}].unit`" as="small" class="err"/>
                  </td>
                  <td>
                    <div class="button-td" style="gap:.5rem">
                      <button type="button" class="btn-add" @click="push({ name:'', quantity:'', unit:'' })">+</button>
                      <button v-if="fields.length > 1" type="button" class="btn-remove" @click="remove(i)">−</button>
                    </div>
                  </td>
                </tr>
              </FieldArray>
              </tbody>
            </table>
          </div>

          <!-- Steps -->
          <div class="section-title">Steps</div>
          <div class="table-container">
            <table class="instructions-table">
              <thead>
              <tr><th style="width:10%;">Step No</th><th>Description</th><th style="width:10%;"></th></tr>
              </thead>
              <tbody>
              <FieldArray name="steps" v-slot="{ fields, push, remove, replace }">
                <tr v-for="(row, i) in fields" :key="row.key">
                  <td class="step-number">{{ i + 1 }}</td>
                  <td>
                    <Field :name="`steps[${i}].description`" as="textarea" rows="2" class="table-textarea" placeholder="Describe this step"/>
                    <ErrorMessage :name="`steps[${i}].description`" as="small" class="err"/>
                  </td>
                  <td>
                    <div class="button-td" style="gap:.5rem">
                      <button type="button" class="btn-add" @click="push({ step_no: fields.length + 1, description: '' })">+</button>
                      <button
                          v-if="fields.length > 1"
                          type="button"
                          class="btn-remove"
                          @click="removeStepAndRenumber(i, fields, replace)"
                      >−</button>
                    </div>
                  </td>
                </tr>
              </FieldArray>
              </tbody>
            </table>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn-cancel" @click="emitClose(resetForm)">Cancel</button>
            <button type="submit" class="btn-save" :disabled="isSubmitting">
              {{ isSubmitting ? 'Saving…' : 'Save Recipe' }}
            </button>
          </div>
        </Form>
      </div>
    </div>
  </div>
</template>

<script setup>
import {ref, computed} from 'vue'
import {Form, Field, ErrorMessage, FieldArray} from 'vee-validate'
import * as yup from 'yup'
import axiosRequest from '../helpers/axios'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  defaults: {
    type: Object,
    default: () => ({
      name: '',
      cuisine_type: '',
      image: null,
      ingredients: [{ name: '', quantity: '', unit: '' }],
      steps: [{ step_no: 1, description: '' }],
    }),
  },
})
const emit = defineEmits(['update:modelValue', 'saved'])

const imagePreview = ref(null)
const fileError = ref('')

const schema = yup.object({
  name: yup.string().required('Recipe name is required'),
  cuisine_type: yup.string().required('Cuisine type is required'),
  ingredients: yup.array().of(
      yup.object({
        name: yup.string().required('Ingredient name is required'),
        quantity: yup.string().nullable(),
        unit: yup.string().nullable(),
      })
  ).min(1, 'At least one ingredient'),
  steps: yup.array().of(
      yup.object({
        step_no: yup.number().required(),
        description: yup.string().required('Step description is required'),
      })
  ).min(1, 'At least one step'),
})

const initialValues = computed(() => structuredClone(props.defaults))

function emitClose(resetForm) {
  emit('update:modelValue', false)
  imagePreview.value = null
  fileError.value = ''
  if (typeof resetForm === 'function') {
    resetForm({ values: initialValues.value })
  }
}

function onImageChange(e, setFieldValue) {
  const file = e.target.files?.[0]
  fileError.value = ''
  if (file) {
    setFieldValue('image', file);
    const reader = new FileReader()
    reader.onload = (ev) => (imagePreview.value = ev.target?.result)
    reader.readAsDataURL(file)
  } else {
    setFieldValue('image', null)
    imagePreview.value = null
  }
}

function removeStepAndRenumber(i, fields, replace) {
  const next = fields
      .filter((_, idx) => idx !== i)
      .map((f, idx) => ({ ...f.value, step_no: idx + 1 }))
  replace(next) // vee-validate will re-render properly
}

async function onSubmit(formValues, { setErrors }) {
  try {
    const form = new FormData()
    form.append('name', formValues.name)
    form.append('cuisine_type', formValues.cuisine_type)
    if (formValues.image) form.append('image', formValues.image)

    formValues.ingredients.forEach((ing, i) => {
      form.append(`ingredients[${i}][name]`, ing.name)
      form.append(`ingredients[${i}][quantity]`, ing.quantity ?? '')
      form.append(`ingredients[${i}][unit]`, ing.unit ?? '')
    })
    formValues.steps.forEach((st, i) => {
      form.append(`steps[${i}][step_no]`, st.step_no)
      form.append(`steps[${i}][description]`, st.description)
    })

    await axiosRequest.post('/v1/recipes', form, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    emit('saved')
    emit('update:modelValue', false)
  } catch (e) {
    const resp = e?.response
    if (resp?.status === 422) {
      const errs = resp.data?.errors || {}
      const mapped = Object.fromEntries(
          Object.entries(errs).map(([k, v]) => [k, Array.isArray(v) ? v[0] : String(v)])
      )
      setErrors(mapped)
    } else if (resp?.status === 401) {
      setErrors({ name: 'Unauthorized. Please login again.' })
    } else {
      setErrors({ name: resp?.data?.message ?? 'Server error' })
    }
  }
}
</script>

<style scoped>

.err {
  color: #dc2626;
}
.button-td {
  display: flex
}

.home-page {
  padding: 3rem 1rem;
}

.text-center {
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 1rem;
}

.page-subtitle {
  font-size: 1.25rem;
  color: #4b5563;
  margin-bottom: 2rem;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background-color: #10b981;
  color: white;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.btn-primary:hover {
  background-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
}

.btn-secondary {
  background-color: #6366f1;
  color: white;
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
}

.btn-secondary:hover {
  background-color: #4f46e5;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(99, 102, 241, 0.4);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.feature-description {
  color: #6b7280;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }

  .page-subtitle {
    font-size: 1rem;
  }
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #1f2937;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  line-height: 1;
  transition: color 0.2s;
}

.close-button:hover {
  color: #1f2937;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-input {
  width: 100%;
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
  font-family: inherit;
}

.form-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel,
.btn-save {
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background-color: #e5e7eb;
  color: #374151;
}

.btn-cancel:hover {
  background-color: #d1d5db;
}

.btn-save {
  background-color: #10b981;
  color: white;
}

.btn-save:hover {
  background-color: #059669;
}

/* Form Row */
.form-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.form-input-file {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  cursor: pointer;
}

.form-input-file:focus {
  outline: none;
  border-color: #10b981;
}

/* Image Preview */
.image-preview {
  margin-top: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
  background-color: #f9fafb;
}

.image-preview img {
  width: 100%;
  height: auto;
  max-height: 200px;
  object-fit: cover;
  display: block;
}

/* Section Title */
.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
  margin-top: 1rem;
}

/* Table Styles */
.table-container {
  overflow-x: auto;
  margin-bottom: 1.5rem;
}

.ingredients-table,
.instructions-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

.ingredients-table th,
.instructions-table th {
  background-color: #f9fafb;
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
  font-size: 0.875rem;
}

.ingredients-table td,
.instructions-table td {
  padding: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.ingredients-table tbody tr:last-child td,
.instructions-table tbody tr:last-child td {
  border-bottom: none;
}

.table-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.table-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
}

.table-textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  resize: vertical;
  font-family: inherit;
}

.table-textarea:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
}

.step-number {
  text-align: center;
  font-weight: 600;
  color: #6b7280;
}

.btn-add,
.btn-remove {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 1.25rem;
  font-weight: bold;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.btn-add {
  background-color: #10b981;
  color: white;
}

.btn-add:hover {
  background-color: #059669;
  transform: scale(1.1);
}

.btn-remove {
  background-color: #ef4444;
  color: white;
}

.btn-remove:hover {
  background-color: #dc2626;
  transform: scale(1.1);
}

/* Responsive */
@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .modal-container {
    max-width: 95%;
  }
}
</style>