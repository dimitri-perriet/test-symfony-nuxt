<template>
  <div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <PageHeader
      title="Auteurs"
      :count="authors.length"
      singular="auteur"
      plural="auteurs"
      add-button-text="Ajouter un auteur"
      button-class="btn-secondary"
      @add="openAddModal"
    />

    <!-- Barre de recherche -->
    <SearchFilters
      v-model:search-query="searchQuery"
      v-model:sort-by="sortBy"
      search-placeholder="Rechercher par nom, prénom..."
      :sort-options="[
        { value: 'nom', label: 'Trier par nom' },
        { value: 'prenom', label: 'Trier par prénom' },
        { value: 'naissance', label: 'Trier par date de naissance' }
      ]"
    />

    <!-- Chargement avec skeletons -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <AuthorCardSkeleton
        v-for="i in 6"
        :key="i"
      />
    </div>

    <!-- Erreur -->
    <ErrorAlert
      v-if="error"
      :message="error"
      show-retry
      @retry="loadData"
    />

    <!-- Liste des auteurs -->
    <div v-else-if="filteredAuthors.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <AuthorCard
        v-for="auteur in filteredAuthors"
        :key="auteur.id"
        :author="auteur"
        :books-count="getAuthorBooksCount(auteur.id)"
        @view-books="showAuthorBooks"
        @edit-author="openEditModal"
        @delete-author="confirmDeleteAuthor"
      />
    </div>

    <!-- Aucun résultat -->
    <EmptyState
      v-else
      :title="searchQuery ? 'Aucun auteur trouvé' : 'Aucun auteur dans la collection'"
      :description="searchQuery ? 'Essayez de modifier vos critères de recherche.' : 'La collection d\'auteurs est vide pour le moment.'"
      :show-action="!searchQuery"
      action-text="Ajouter votre premier auteur"
      action-button-class="btn-secondary"
      :show-clear-filters="!!searchQuery"
      @action="openAddModal"
      @clear-filters="clearSearch"
    >
      <template #icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
      </template>
    </EmptyState>

    <!-- Modal pour afficher les livres d'un auteur -->
    <dialog ref="booksModal" class="modal">
      <div class="modal-box w-11/12 max-w-4xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-lg">
            Livres de {{ selectedAuthor?.prenom }} {{ selectedAuthor?.nom }}
          </h3>
          <button @click="closeBooksModal" class="btn btn-sm btn-circle btn-ghost">✕</button>
        </div>
        
        <div v-if="authorBooks.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="livre in authorBooks"
            :key="livre.id"
            class="card bg-base-200 shadow-md"
          >
            <div class="card-body p-4">
              <h4 class="card-title text-base">{{ livre.titre }}</h4>
              <p v-if="livre.resume" class="text-sm opacity-70 line-clamp-2">
                {{ livre.resume }}
              </p>
              <div class="flex justify-between items-center mt-2">
                <div v-if="livre.categorie" class="badge badge-primary badge-sm">
                  {{ livre.categorie.nom }}
                </div>
                <div class="text-xs opacity-60">
                  {{ livre.datePublication ? formatDate(livre.datePublication) : '' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8 opacity-70">
          Aucun livre trouvé pour cet auteur.
        </div>
      </div>
      <form method="dialog" class="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>

    <!-- Modal d'ajout/modification d'auteur -->
    <AuthorModal
      :is-open="isAuthorModalOpen"
      :author="selectedAuthorForEdit"
      @close="closeAuthorModal"
      @success="handleAuthorSaved"
    />

    <!-- Modal de confirmation de suppression -->
    <ConfirmationModal
      :is-open="isDeleteModalOpen"
      :title="deleteModalTitle"
      :message="deleteModalMessage"
      :cascade-warning="deleteModalCascadeWarning"
      :is-loading="isDeleting"
      @confirm="performDelete"
      @cancel="closeDeleteModal"
    />
  </div>
</template>

<script setup lang="ts">
const { getAuthors, getBooks } = useApi()
const { isAuthenticated, isAdmin } = useAuth()
const { showSuccess, showError } = useToast()

// Meta
useHead({
  title: 'Auteurs - Bibliothèque',
  meta: [
    {
      name: 'description',
      content: 'Découvrez les biographies de nos auteurs talentueux.'
    }
  ]
})

// État 
const authors = ref<any[]>([])
const books = ref<any[]>([])
const pending = ref(true)
const error = ref<string | null>(null)
const searchQuery = ref('')
const sortBy = ref('nom')
const selectedAuthor = ref<any | null>(null)
const authorBooks = ref<any[]>([])
const booksModal = ref<any>(null)

// État pour le modal d'ajout/modification d'auteur
const isAuthorModalOpen = ref(false)
const selectedAuthorForEdit = ref<any | null>(null)

// Gestion de la modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const authorToDelete = ref<any | null>(null)

const deleteModalTitle = computed(() => {
  if (!authorToDelete.value) return ''
  return `Supprimer l'auteur`
})

const deleteModalMessage = computed(() => {
  if (!authorToDelete.value) return ''
  return `Êtes-vous sûr de vouloir supprimer "${authorToDelete.value.prenom} ${authorToDelete.value.nom}" ?`
})

const deleteModalCascadeWarning = computed(() => {
  if (!authorToDelete.value) return ''
  const booksCount = getAuthorBooksCount(authorToDelete.value.id)
  return booksCount > 0 
    ? `Cette action supprimera également ${booksCount} livre(s) associé(s) de façon définitive !`
    : ''
})

// Fonction pour formater les dates (pour la modal)
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Compter les livres d'un auteur
const getAuthorBooksCount = (authorId: number) => {
  return books.value.filter(livre => livre.auteur?.id === authorId).length
}

// Auteurs filtrés et triés
const filteredAuthors = computed(() => {
  let filtered = authors.value

  // Filtre par recherche
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(auteur => 
      auteur.nom?.toLowerCase().includes(query) ||
      auteur.prenom?.toLowerCase().includes(query) ||
      auteur.biographie?.toLowerCase().includes(query)
    )
  }

  // Tri
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'prenom':
        return (a.prenom || '').localeCompare(b.prenom || '')
      case 'naissance':
        if (!a.dateNaissance && !b.dateNaissance) return 0
        if (!a.dateNaissance) return 1
        if (!b.dateNaissance) return -1
        return new Date(a.dateNaissance).getTime() - new Date(b.dateNaissance).getTime()
      default: // nom
        return (a.nom || '').localeCompare(b.nom || '')
    }
  })

  return filtered
})

// Effacer la recherche
const clearSearch = () => {
  searchQuery.value = ''
}

// Afficher les livres d'un auteur
const showAuthorBooks = (auteur: any) => {
  selectedAuthor.value = auteur
  authorBooks.value = books.value.filter(livre => livre.auteur?.id === auteur.id)
  booksModal.value?.showModal()
}

// Fermer la modal des livres
const closeBooksModal = () => {
  booksModal.value?.close()
  selectedAuthor.value = null
  authorBooks.value = []
}

// Fonctions pour le modal d'auteur
const openAddModal = () => {
  selectedAuthorForEdit.value = null
  isAuthorModalOpen.value = true
}

const openEditModal = (author: any) => {
  selectedAuthorForEdit.value = author
  isAuthorModalOpen.value = true
}

const closeAuthorModal = () => {
  isAuthorModalOpen.value = false
  selectedAuthorForEdit.value = null
}

const handleAuthorSaved = (author: any) => {
  // Recharger les données pour voir le nouvel auteur
  loadData()
}

// Nouvelle fonction de confirmation de suppression avec modale
const confirmDeleteAuthor = (author: any) => {
  authorToDelete.value = author
  isDeleteModalOpen.value = true
}

// Fonction pour fermer la modale de suppression
const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  authorToDelete.value = null
  isDeleting.value = false
}

// Fonction de suppression effective
const performDelete = async () => {
  if (!authorToDelete.value) return
  
  try {
    isDeleting.value = true
    
    await $fetch(`http://localhost:8000/api/auteurs/${authorToDelete.value.id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/ld+json',
        'Authorization': `Bearer ${useAuth().token.value}`
      }
    })
    
    // Recharger les données
    await loadData()
    
    // Afficher un message de succès
    showSuccess(
      'Auteur supprimé', 
      `"${authorToDelete.value.prenom} ${authorToDelete.value.nom}" a été supprimé avec succès`
    )
    
    // Fermer la modale
    closeDeleteModal()
    
  } catch (err: any) {
    console.error('Erreur lors de la suppression:', err)
    showError(
      'Erreur de suppression',
      err.data?.detail || 'Une erreur est survenue lors de la suppression'
    )
    isDeleting.value = false
  }
}

// Charger les données
const loadData = async () => {
  try {
    pending.value = true
    error.value = null
    
    const [authorsData, booksData] = await Promise.all([
      getAuthors(),
      getBooks()
    ])
    
    authors.value = authorsData
    books.value = booksData
  } catch (err) {
    error.value = 'Impossible de charger les données. Veuillez réessayer.'
    console.error('Erreur lors du chargement:', err)
  } finally {
    pending.value = false
  }
}

// Charger les données au montage
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
