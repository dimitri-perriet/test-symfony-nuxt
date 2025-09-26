<template>
  <div class="card bg-base-100 shadow-xl mb-8">
    <div class="card-body">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Recherche -->
        <div class="flex-1">
          <label class="input input-bordered flex items-center gap-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 16 16"
              fill="currentColor"
              class="h-4 w-4 opacity-70">
              <path
                fill-rule="evenodd"
                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                clip-rule="evenodd" />
            </svg>
            <input 
              :value="searchQuery"
              @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
              type="text" 
              class="grow" 
              :placeholder="searchPlaceholder"
            />
          </label>
        </div>

        <!-- Sélecteur de tri -->
        <div v-if="sortOptions.length > 0" class="lg:w-64">
          <select 
            :value="sortBy" 
            @change="$emit('update:sortBy', ($event.target as HTMLSelectElement).value)"
            class="select select-bordered w-full"
          >
            <option v-for="option in sortOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Filtre par catégorie -->
        <div v-if="categories && categories.length > 0" class="lg:w-64">
          <select 
            :value="selectedCategory"
            @change="$emit('update:selectedCategory', ($event.target as HTMLSelectElement).value)"
            class="select select-bordered w-full"
          >
            <option value="">Toutes les catégories</option>
            <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
              {{ categorie.nom }}
            </option>
          </select>
        </div>

        <!-- Sélecteur de vue (grille/liste) -->
        <div v-if="showViewToggle" class="join">
          <button 
            @click="$emit('update:viewMode', 'grid')"
            :class="['btn join-item', viewMode === 'grid' ? 'btn-active' : '']"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
          </button>
          <button 
            @click="$emit('update:viewMode', 'list')"
            :class="['btn join-item', viewMode === 'list' ? 'btn-active' : '']"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface SortOption {
  value: string
  label: string
}

interface Category {
  id: number
  nom: string
}

interface Props {
  searchQuery: string
  searchPlaceholder: string
  sortBy?: string
  sortOptions?: SortOption[]
  selectedCategory?: string
  categories?: Category[]
  viewMode?: 'grid' | 'list'
  showViewToggle?: boolean
}

withDefaults(defineProps<Props>(), {
  sortOptions: () => [],
  categories: () => [],
  showViewToggle: false,
  viewMode: 'grid'
})

defineEmits<{
  'update:searchQuery': [value: string]
  'update:sortBy': [value: string]
  'update:selectedCategory': [value: string]
  'update:viewMode': [value: 'grid' | 'list']
}>()
</script>
