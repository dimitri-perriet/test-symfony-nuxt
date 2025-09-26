<template>
  <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-200">
    <div class="card-body">
      <!-- En-tête avec couleur -->
      <div class="flex items-center gap-4 mb-4">
        <div 
          class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
          :style="{ backgroundColor: category.couleur || '#6B7280' }"
        >
          {{ category.nom?.charAt(0).toUpperCase() }}
        </div>
        <div>
          <h2 class="card-title">{{ category.nom }}</h2>
          <div class="badge badge-ghost badge-sm">
            {{ booksCount }} {{ booksCount <= 1 ? 'livre' : 'livres' }}
          </div>
        </div>
      </div>

      <!-- Description -->
      <div v-if="category.description" class="mb-4">
        <p class="text-base-content/70 text-sm line-clamp-3">
          {{ category.description }}
        </p>
      </div>

      <!-- Actions -->
      <div class="card-actions justify-between items-center">
        <button 
          @click="$emit('view-books', category)"
          class="btn btn-primary btn-sm"
          :disabled="booksCount === 0"
        >
          Voir les livres
        </button>
        <div class="badge badge-ghost badge-sm">
          ID: {{ category.id }}
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
}>()
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
