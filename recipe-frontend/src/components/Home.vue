<template>
  <div class="home-page">
    <div style="margin-top: 2%">
      <div class="filters">
        <input v-model="filters.name" type="text" class="filter-input" placeholder="Filter by recipe name"
               @input="handleFilters"/>
        <input v-model="filters.cuisine_type" type="text" class="filter-input" placeholder="Filter by cuisine type"
               @input="handleFilters"/>
        <div style="display:flex;gap:.5rem;">
          <button class="btn-secondary" @click="resetFilters" :disabled="loading">
            <i class="pi pi-refresh" style="font-size: 1rem"></i>
            Reset</button>
          <button class="btn-primary" @click="openCreate">
            <i class="pi pi-save" style="font-size: 1rem"></i> New Recipe</button>
        </div>
      </div>

      <div style="text-align:center;">
        <div v-if="error" class="status error">{{ error }}</div>
        <div v-else-if="recipes.length === 0" class="status">No recipes found.</div>
      </div>

      <div v-if="!loading && recipes.length" class="recipes-grid">
        <div v-for="r in recipes" :key="r.id" class="recipe-card">
          <div class="thumb">
            <img :src="r.image_url || r.image || fallbackImage" alt=""/>
          </div>
          <div class="card-body">
            <h3 class="title">{{ r.name }}</h3>
            <p class="muted">{{ r.cuisine_type || '—' }}</p>
            <div class="meta">
              <span><i class="pi pi-sitemap"></i> {{ r.ingredients_count ?? r.ingredients?.length ?? 0 }} ingredients</span>
              <span><i class="pi pi-database"></i> {{ r.steps_count ?? r.steps?.length ?? 0 }} steps</span>
            </div>
            &nbsp;
            <div class="meta"><span><i class="pi pi-clock"></i>&nbsp;{{ r.created_at }}</span></div>

            <!-- Actions -->
            <div style="display:flex;gap:.5rem;margin-top:.75rem;">
<!--              <button class="btn-secondary" @click="openEdit(r)">Edit</button>-->
              <button class="btn-danger" :disabled="deletingId === r.id" @click="deleteRecipe(r.id)">
                <i class="pi pi-trash"></i> {{ deletingId === r.id ? 'Deleting…' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!loading && pagination.total > pagination.per_page" class="pagination">
      <button class="btn-secondary" :disabled="!pagination.prev_page_url" @click="goPage(pagination.current_page - 1)">‹
        Prev
      </button>
      <span class="page-info">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
      <button class="btn-secondary" :disabled="!pagination.next_page_url" @click="goPage(pagination.current_page + 1)">
        Next ›
      </button>
    </div>

    <!-- Create -->
    <RecipeModal v-model="showCreate" mode="create" @saved="onSaved"/>
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import axiosRequest from '../helpers/axios.js'
import RecipeModal from './RecipeModal.vue'

const showCreate = ref(false)
const showEdit = ref(false)
const editingRecipe = ref(null)         // raw recipe from API (selected)
const editingDefaults = ref(null)       // mapped defaults for modal
const deletingId = ref(null)

const loading = ref(false)
const error = ref('')
const recipes = ref([])
const fallbackImage = 'https://via.placeholder.com/600x400?text=Recipe'

const pagination = ref({
  total: 0, per_page: 15, current_page: 1, last_page: 1, next_page_url: null, prev_page_url: null,
})

const filters = ref({name: '', cuisine_type: ''})
let debounceId = null

function handleFilters() {
  clearTimeout(debounceId)
  debounceId = setTimeout(() => {
    pagination.value.current_page = 1
    fetchRecipes()
  }, 350)
}

function resetFilters() {
  filters.value = {name: '', cuisine_type: ''}
  pagination.value.current_page = 1
  fetchRecipes()
}

async function fetchRecipes() {
  loading.value = true
  error.value = ''
  try {
    const params = {
      name: filters.value.name || undefined,
      cuisine_type: filters.value.cuisine_type || undefined,
      page: pagination.value.current_page,
    }
    const res = await axiosRequest.get('/v1/recipes', {params})
    const payload = res.data?.data

    if (Array.isArray(payload)) {
      recipes.value = payload
      pagination.value = {
        total: payload.length,
        per_page: payload.length,
        current_page: 1,
        last_page: 1,
        next_page_url: null,
        prev_page_url: null
      }
    } else if (payload?.data && payload?.meta) {
      recipes.value = payload.data
      const m = payload.meta, l = payload.links || {}
      pagination.value = {
        total: m.total,
        per_page: m.per_page,
        current_page: m.current_page,
        last_page: m.last_page,
        next_page_url: l.next ?? null,
        prev_page_url: l.prev ?? null
      }
    } else {
      recipes.value = payload ?? []
    }
  } catch (e) {
    error.value = e?.response?.data?.message || 'Failed to fetch recipes.'
  } finally {
    loading.value = false
  }
}

function goPage(p) {
  if (p < 1 || p > pagination.value.last_page) return
  pagination.value.current_page = p
  fetchRecipes()
}

function openCreate() {
  showCreate.value = true
}

function openEdit(recipe) {
  editingRecipe.value = recipe
  // Map API shape -> modal defaults
  editingDefaults.value = {
    name: recipe.name ?? '',
    cuisine_type: recipe.cuisine_type ?? '',
    image: null,
    ingredients: (recipe.ingredients ?? []).map(i => ({
      name: i.name ?? '',
      quantity: i.quantity ?? '',
      unit: i.unit ?? '',
    })),
    steps: (recipe.steps ?? []).map((s, idx) => ({
      step_no: Number(s.step_no ?? (idx + 1)),
      description: s.description ?? '',
    })),
  }
  showEdit.value = true
}

async function deleteRecipe(id) {
  if (!confirm('Delete this recipe?')) return
  const idx = recipes.value.findIndex(r => r.id === id)
  const backup = idx >= 0 ? recipes.value[idx] : null
  if (idx >= 0) recipes.value.splice(idx, 1)
  deletingId.value = id
  try {
    await axiosRequest.delete(`/v1/recipes/${id}`)
  } catch (e) {
    // rollback if failed
    if (idx >= 0 && backup) recipes.value.splice(idx, 0, backup)
    alert(e?.response?.data?.message || 'Failed to delete')
  } finally {
    deletingId.value = null
  }
}

function onSaved() {
  fetchRecipes()
}

onMounted(fetchRecipes)
</script>

<style scoped>

.filters {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: .75rem;
  max-width: 900px;
  margin: 0 auto 1.5rem;
  padding: 0 1rem;
}

.filter-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
}

.filter-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
}

/* Status text */
.status {
  margin: .75rem 0 1.25rem;
  color: #4b5563;
}

.status.error {
  color: #ef4444;
}

/* Recipes grid */
.recipes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1rem;
  max-width: 1200px;
  margin: 0 auto 2rem;
  padding: 0 1rem;
}

.recipe-card {
  background: #fff;
  border-radius: .5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.thumb {
  width: 100%;
  aspect-ratio: 3 / 2;
  background: #f3f4f6;
  overflow: hidden;
}

.thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.card-body {
  padding: .875rem .875rem 1rem;
}

.title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: .25rem;
}

.muted {
  color: #6b7280;
  font-size: .92rem;
  margin-bottom: .5rem;
}

.meta {
  display: flex;
  gap: .75rem;
  color: #374151;
  font-size: .9rem;
}

/* Pagination */
.pagination {
  display: flex;
  gap: .75rem;
  align-items: center;
  justify-content: center;
  margin-bottom: 2rem;
}

.page-info {
  color: #374151;
}

/* Buttons reused (you already had these classes) */
.btn-primary,
.btn-secondary {
  padding: 0.6rem 1rem;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all .2s ease;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
}

.btn-primary {
  background: #10b981;
  color: #fff;
}

.btn-primary:hover {
  background: #059669;
}

.btn-secondary {
  background: #6366f1;
  color: #fff;
}

.btn-secondary:hover {
  background: #4f46e5;
}

@media (max-width: 768px) {
  .filters {
    grid-template-columns: 1fr;
  }
}
</style>