<template>
  <div class="modal" :class="{ 'modal-open': isOpen }">
    <div class="modal-box">
      <div class="flex items-start gap-4">
        <!-- Icône d'avertissement -->
        <div class="flex-shrink-0">
          <div class="w-12 h-12 rounded-full bg-warning/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
        </div>

        <!-- Contenu -->
        <div class="flex-1">
          <h3 class="font-bold text-lg mb-2">{{ title }}</h3>
          <div class="text-sm opacity-75 whitespace-pre-line">{{ message }}</div>
          
          <!-- Informations supplémentaires si cascade -->
          <div v-if="cascadeWarning" class="mt-4 p-3 bg-error/10 border border-error/20 rounded-lg">
            <div class="flex items-center gap-2 text-error font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <span>Suppression en cascade</span>
            </div>
            <p class="text-sm mt-1 text-error/80">{{ cascadeWarning }}</p>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="modal-action">
        <button 
          class="btn btn-ghost"
          @click="cancel"
          :disabled="isLoading"
        >
          Annuler
        </button>
        <button 
          class="btn btn-error"
          @click="confirm"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="loading loading-spinner loading-sm"></span>
          {{ confirmText || 'Supprimer' }}
        </button>
      </div>
    </div>
    <div class="modal-backdrop" @click="cancel"></div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  isOpen: boolean
  title: string
  message: string
  cascadeWarning?: string
  confirmText?: string
  isLoading?: boolean
}

defineProps<Props>()

const emit = defineEmits<{
  'confirm': []
  'cancel': []
}>()

const confirm = () => {
  emit('confirm')
}

const cancel = () => {
  emit('cancel')
}
</script>
