<template>
  <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-200">
    <div class="card-body">
      <!-- Avatar et nom -->
      <div class="flex items-center gap-4 mb-4">
        <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full flex items-center justify-center">
          <span class="text-xl font-bold">{{ getInitials(author) }}</span>
        </div>
        <div>
          <h2 class="card-title">
            {{ author.prenom }} {{ author.nom }}
          </h2>
          <p class="text-base-content/70 text-sm">
            {{ formatAuthorStatus(author) }}
          </p>
        </div>
      </div>

      <!-- Dates -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div v-if="author.dateNaissance" class="stat">
          <div class="stat-title text-xs">Naissance</div>
          <div class="stat-value text-sm">{{ formatDate(author.dateNaissance) }}</div>
        </div>
        <div v-if="author.dateDeces" class="stat">
          <div class="stat-title text-xs">Décès</div>
          <div class="stat-value text-sm">{{ formatDate(author.dateDeces) }}</div>
        </div>
      </div>

      <!-- Biographie -->
      <div v-if="author.biographie" class="mb-4">
        <p class="text-base-content/70 text-sm line-clamp-3">
          {{ author.biographie }}
        </p>
      </div>

      <!-- Nombre de livres -->
      <div class="flex items-center gap-2 mb-4">
        <svg class="h-4 w-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <span class="text-base-content/70 text-sm">
          {{ booksCount }} {{ booksCount <= 1 ? 'livre' : 'livres' }}
        </span>
      </div>

      <!-- Actions -->
      <div class="card-actions justify-between items-center">
        <div class="flex gap-2">
          <button 
            @click="$emit('view-books', author)"
            class="btn btn-primary btn-sm"
            :disabled="booksCount === 0"
          >
            Voir les livres
          </button>
          <!-- Bouton d'édition (visible si admin) -->
          <button 
            v-if="isAdmin"
            @click="$emit('edit-author', author)"
            class="btn btn-secondary btn-sm"
            title="Modifier l'auteur"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <!-- Bouton de suppression (visible si admin) -->
          <button 
            v-if="isAdmin"
            @click="$emit('delete-author', author)"
            class="btn btn-error btn-sm"
            title="Supprimer l'auteur"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
        <div class="badge badge-ghost badge-sm">
          ID: {{ author.id }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Author {
  id: number
  nom: string
  prenom: string
  biographie?: string
  dateNaissance?: string
  dateDeces?: string
}

interface Props {
  author: Author
  booksCount: number
}

defineProps<Props>()

defineEmits<{
  'view-books': [author: Author]
  'edit-author': [author: Author]
  'delete-author': [author: Author]
}>()

// Authentification
const { isAuthenticated, isAdmin } = useAuth()

// Fonction pour obtenir les initiales
const getInitials = (author: Author) => {
  const prenom = author.prenom?.charAt(0) || ''
  const nom = author.nom?.charAt(0) || ''
  return (prenom + nom).toUpperCase()
}

// Fonction pour formater le statut de l'auteur
const formatAuthorStatus = (author: Author) => {
  if (author.dateDeces) {
    return `Décédé${author.dateDeces ? ' en ' + new Date(author.dateDeces).getFullYear() : ''}`
  }
  if (author.dateNaissance) {
    const age = new Date().getFullYear() - new Date(author.dateNaissance).getFullYear()
    return `${age} ans`
  }
  return 'Auteur'
}

// Fonction pour formater les dates
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
