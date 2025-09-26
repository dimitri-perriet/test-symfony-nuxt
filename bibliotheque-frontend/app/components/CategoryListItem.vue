<template>
  <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-200">
    <div class="card-body">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div 
            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
            :style="{ backgroundColor: category.couleur || '#6B7280' }"
          >
            {{ category.nom?.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h3 class="font-bold text-lg">{{ category.nom }}</h3>
            <p v-if="category.description" class="text-base-content/70 text-sm line-clamp-1">
              {{ category.description }}
            </p>
          </div>
        </div>
        
        <div class="flex items-center gap-4">
          <div class="stats">
            <div class="stat px-4">
              <div class="stat-title text-xs">Livres</div>
              <div class="stat-value text-lg">{{ booksCount }}</div>
            </div>
          </div>
          
          <div class="flex gap-2">
            <button 
              @click="$emit('view-books', category)"
              class="btn btn-primary btn-sm"
              :disabled="booksCount === 0"
            >
              Voir les livres
            </button>
            <!-- Bouton d'édition (visible si admin) -->
            <button 
              v-if="isAdmin"
              @click="$emit('edit-category', category)"
              class="btn btn-accent btn-sm"
              title="Modifier la catégorie"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <!-- Bouton de suppression (visible si admin) -->
            <button 
              v-if="isAdmin"
              @click="$emit('delete-category', category)"
              class="btn btn-error btn-sm"
              title="Supprimer la catégorie"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Category {
  id: number
  nom: string
  description?: string
  couleur?: string
}

interface Props {
  category: Category
  booksCount: number
}

defineProps<Props>()

defineEmits<{
  'view-books': [category: Category]
  'edit-category': [category: Category]
  'delete-category': [category: Category]
}>()

// Authentification
const { isAuthenticated, isAdmin } = useAuth()
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
