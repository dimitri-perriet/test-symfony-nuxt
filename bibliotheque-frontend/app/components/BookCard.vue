<template>
  <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-200">
    <div class="card-body">
      <!-- Titre du livre -->
      <h2 class="card-title line-clamp-2">
        {{ book.titre }}
      </h2>

      <!-- Auteur -->
      <div v-if="book.auteur" class="flex items-center mb-2">
        <svg class="h-4 w-4 opacity-70 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="text-base-content/70">
          {{ book.auteur.prenom }} {{ book.auteur.nom }}
        </span>
      </div>

      <!-- Catégorie -->
      <div v-if="book.categorie" class="mb-3">
        <div 
          class="badge badge-primary"
          :style="{ 
            backgroundColor: book.categorie.couleur || 'oklch(var(--p))', 
            borderColor: book.categorie.couleur || 'oklch(var(--p))'
          }"
        >
          {{ book.categorie.nom }}
        </div>
      </div>

      <!-- Résumé -->
      <p v-if="book.resume" class="text-base-content/70 text-sm mb-3 line-clamp-3">
        {{ book.resume }}
      </p>

      <!-- Métadonnées -->
      <div class="space-y-1 mb-4">
        <!-- ISBN -->
        <div v-if="book.isbn" class="text-xs opacity-60">
          ISBN: {{ book.isbn }}
        </div>

        <!-- Date de publication -->
        <div v-if="book.datePublication" class="text-xs opacity-60">
          Publié le {{ formatDate(book.datePublication) }}
        </div>
      </div>

      <!-- Actions -->
      <div class="card-actions justify-between items-center">
        <button 
          @click="$emit('view-details', book)"
          class="btn btn-primary btn-sm"
        >
          Voir détails
        </button>
        <div class="badge badge-ghost badge-sm">
          ID: {{ book.id }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Book {
  id: number
  titre: string
  isbn?: string
  resume?: string
  datePublication?: string
  auteur?: {
    id: number
    nom: string
    prenom: string
  }
  categorie?: {
    id: number
    nom: string
    couleur?: string
  }
}

interface Props {
  book: Book
}

defineProps<Props>()

defineEmits<{
  'view-details': [book: Book]
}>()

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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
