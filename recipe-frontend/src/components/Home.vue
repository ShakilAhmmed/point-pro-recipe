<template>
  <div class="home-page">
    <div class="text-center">
      <h1 class="page-title">Recipe Management</h1>
      
      <!-- Action Buttons -->
      <div class="action-buttons">
        <button class="btn-primary" @click="openModal">
          New Recipe
        </button>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🚀</div>
          <h3 class="feature-title">Fast</h3>
          <p class="feature-description">
            Lightning-fast performance for the best user experience
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🔒</div>
          <h3 class="feature-title">Secure</h3>
          <p class="feature-description">
            Your data is protected with top-tier security
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">💡</div>
          <h3 class="feature-title">Simple</h3>
          <p class="feature-description">
            Easy to use interface for everyone
          </p>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">Create New Recipe</h2>
          <button class="close-button" @click="closeModal">✕</button>
        </div>
        
        <div class="modal-body">
          <!-- First Row: Recipe Name, Cuisine Type, Image Upload -->
          <div class="form-row">
            <div class="form-group">
              <label for="recipe-name">Recipe Name</label>
              <input
                id="recipe-name"
                v-model="recipeName"
                type="text"
                placeholder="Enter recipe name"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="cuisine-type">Cuisine Type</label>
              <input
                id="cuisine-type"
                v-model="cuisineType"
                type="text"
                placeholder="e.g., Italian, Chinese"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="image-upload">Image Upload</label>
              <input
                id="image-upload"
                type="file"
                accept="image/*"
                @change="handleImageUpload"
                class="form-input-file"
              />
              <!-- Image Preview -->
              <div v-if="imagePreview" class="image-preview">
                <img :src="imagePreview" alt="Recipe preview" />
              </div>
            </div>
          </div>

          <!-- Ingredients Section -->
          <div class="section-title">Ingredients</div>
          <div class="table-container">
            <table class="ingredients-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Quantity</th>
                  <th>Unit</th>
                  <th style="width: 50px;"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(ingredient, index) in ingredients" :key="index">
                  <td>
                    <input
                      v-model="ingredient.name"
                      type="text"
                      placeholder="Ingredient name"
                      class="table-input"
                    />
                  </td>
                  <td>
                    <input
                      v-model="ingredient.quantity"
                      type="text"
                      placeholder="Amount"
                      class="table-input"
                    />
                  </td>
                  <td>
                    <input
                      v-model="ingredient.unit"
                      type="text"
                      placeholder="Unit"
                      class="table-input"
                    />
                  </td>
                  <td>
                    <button 
                      v-if="index === ingredients.length - 1"
                      @click="addIngredient"
                      class="btn-add"
                      type="button"
                    >
                      +
                    </button>
                    <button 
                      v-if="ingredients.length > 1"
                      @click="removeIngredient(index)"
                      class="btn-remove"
                      type="button"
                    >
                      −
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Instructions Section -->
          <div class="section-title">Instructions</div>
          <div class="table-container">
            <table class="instructions-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Step No</th>
                  <th>Description</th>
                  <th style="width: 50px;"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(instruction, index) in instructions" :key="index">
                  <td class="step-number">{{ instruction.step_number }}</td>
                  <td>
                    <textarea
                      v-model="instruction.description"
                      placeholder="Describe this step"
                      class="table-textarea"
                      rows="2"
                    ></textarea>
                  </td>
                  <td>
                    <button 
                      v-if="index === instructions.length - 1"
                      @click="addInstruction"
                      class="btn-add"
                      type="button"
                    >
                      +
                    </button>
                    <button 
                      v-if="instructions.length > 1"
                      @click="removeInstruction(index)"
                      class="btn-remove"
                      type="button"
                    >
                      −
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-cancel" @click="closeModal">Cancel</button>
          <button class="btn-save" @click="saveRecipe">Save Recipe</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      showModal: false,
      recipeName: '',
      cuisineType: '',
      recipeImage: null,
      imagePreview: null,
      ingredients: [
        { name: '', quantity: '', unit: '' }
      ],
      instructions: [
        { step_number: 1, description: '' }
      ]
    }
  },
  methods: {
    openModal() {
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      // Reset form
      this.recipeName = '';
      this.cuisineType = '';
      this.recipeImage = null;
      this.imagePreview = null;
      this.ingredients = [{ name: '', quantity: '', unit: '' }];
      this.instructions = [{ step_number: 1, description: '' }];
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Store the file object
        this.recipeImage = file;
        console.log('Image uploaded:', file.name);
        
        // Create image preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    addIngredient() {
      this.ingredients.push({ name: '', quantity: '', unit: '' });
    },
    removeIngredient(index) {
      if (this.ingredients.length > 1) {
        this.ingredients.splice(index, 1);
      }
    },
    addInstruction() {
      const nextStepNumber = this.instructions.length + 1;
      this.instructions.push({ step_number: nextStepNumber, description: '' });
    },
    removeInstruction(index) {
      if (this.instructions.length > 1) {
        this.instructions.splice(index, 1);
        // Re-number the remaining instructions
        this.instructions.forEach((instruction, idx) => {
          instruction.step_number = idx + 1;
        });
      }
    },
    saveRecipe() {
      if (!this.recipeName) {
        alert('Please enter a recipe name');
        return;
      }

      if (!this.cuisineType) {
        alert('Please enter a cuisine type');
        return;
      }

      // Filter out empty ingredients and instructions
      const filteredIngredients = this.ingredients.filter(
        i => i.name || i.quantity || i.unit
      );
      
      const filteredInstructions = this.instructions.filter(
        i => i.description
      );

      // Create the recipe data object in the exact format you need
      const recipeData = {
        name: this.recipeName,
        cuisineType: this.cuisineType,
        image: this.recipeImage,
        ingredients: filteredIngredients,
        instructions: filteredInstructions
      };

      console.log('Recipe Data:', recipeData);

      // If you need to send this to an API with image upload
      this.sendToAPI(recipeData);
    },

    async sendToAPI(recipeData) {
      // Create FormData for file upload
      const formData = new FormData();
      
      // Add the image file if it exists
      if (recipeData.image) {
        formData.append('image', recipeData.image);
      }
      
      // Add other data as JSON string
      formData.append('name', recipeData.name);
      formData.append('cuisineType', recipeData.cuisineType);
      formData.append('ingredients', JSON.stringify(recipeData.ingredients));
      formData.append('instructions', JSON.stringify(recipeData.instructions));

      // Log what will be sent
      console.log('FormData ready to send:');
      console.log('- name:', recipeData.name);
      console.log('- cuisineType:', recipeData.cuisineType);
      console.log('- image:', recipeData.image ? recipeData.image.name : null);
      console.log('- ingredients:', JSON.stringify(recipeData.ingredients, null, 2));
      console.log('- instructions:', JSON.stringify(recipeData.instructions, null, 2));

      
      try {
        const response = await fetch('http://192.168.117.5', {
          method: 'POST',
          body: formData
        });

        if (response.ok) {
          const result = await response.json();
          console.log('Recipe saved:', result);
          alert('Recipe saved successfully!');
          this.closeModal();
        } else {
          alert('Failed to save recipe');
        }
      } catch (error) {
        console.error('Error saving recipe:', error);
        alert('Error saving recipe');
      }
      

      // For now, just show success
      alert('Recipe saved successfully!');
      this.closeModal();
    },
    handleViewAll() {
      alert('View All Recipes button clicked!');
      // Add your logic here
    }
  }
}
</script>

<style scoped>
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