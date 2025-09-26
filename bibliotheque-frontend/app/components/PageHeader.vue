<template>
  <div class="hero bg-base-200 rounded-box mb-8">
    <div class="hero-content text-center">
      <div class="max-w-md">
        <h1 class="text-5xl font-bold">{{ title }}</h1>
        <p class="py-6">
          {{ count }} {{ count <= 1 ? singular : plural }} dans notre collection
        </p>
        <!-- Bouton d'ajout (visible si connecté) -->
        <div v-if="isAuthenticated && showAddButton" class="mt-4">
          <button 
            :class="`btn ${buttonClass} btn-lg`" 
            @click="$emit('add')"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ addButtonText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  count: number
  singular: string
  plural: string
  showAddButton?: boolean
  addButtonText?: string
  buttonClass?: string
}

withDefaults(defineProps<Props>(), {
  showAddButton: true,
  addButtonText: 'Ajouter',
  buttonClass: 'btn-primary'
})

defineEmits<{
  'add': []
}>()

const { isAuthenticated } = useAuth()
</script>
