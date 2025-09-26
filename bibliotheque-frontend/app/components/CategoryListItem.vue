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
          
          <button 
            @click="$emit('view-books', category)"
            class="btn btn-primary btn-sm"
            :disabled="booksCount === 0"
          >
            Voir les livres
          </button>
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
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
