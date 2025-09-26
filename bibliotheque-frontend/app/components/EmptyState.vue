<template>
  <div class="hero min-h-96">
    <div class="hero-content text-center">
      <div class="max-w-md">
        <!-- Icône -->
        <div class="w-24 h-24 mx-auto mb-4 opacity-20">
          <slot name="icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </slot>
        </div>
        
        <!-- Titre -->
        <h1 class="text-3xl font-bold text-base-content/60 mb-4">{{ title }}</h1>
        
        <!-- Description -->
        <p class="text-base-content/40 mb-6">{{ description }}</p>
        
        <!-- Action -->
        <div v-if="showAction && isAuthenticated" class="space-y-4">
          <button 
            :class="`btn ${actionButtonClass} btn-lg`"
            @click="$emit('action')"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ actionText }}
          </button>
        </div>

        <!-- Action alternative pour réinitialiser les filtres -->
        <div v-if="showClearFilters" class="mt-4">
          <button class="btn btn-ghost" @click="$emit('clear-filters')">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Effacer les filtres
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  description: string
  showAction?: boolean
  actionText?: string
  actionButtonClass?: string
  showClearFilters?: boolean
}

withDefaults(defineProps<Props>(), {
  showAction: false,
  actionText: 'Ajouter',
  actionButtonClass: 'btn-primary',
  showClearFilters: false
})

defineEmits<{
  'action': []
  'clear-filters': []
}>()

const { isAuthenticated } = useAuth()
</script>
