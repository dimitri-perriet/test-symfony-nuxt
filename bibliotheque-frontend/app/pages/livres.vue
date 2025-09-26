<template>
  <div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <PageHeader
      title="Catalogue des livres"
      :count="books.length"
      singular="livre"
      plural="livres"
      add-button-text="Ajouter un livre"
      button-class="btn-primary"
      @add="openAddModal"
    />

    <!-- Barre de recherche et filtres -->
    <SearchFilters
      v-model:search-query="searchQuery"
      v-model:selected-category="selectedCategory"
      search-placeholder="Rechercher par titre, auteur, ISBN..."
      :categories="categories"
    />

    <!-- Chargement avec skeletons -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <BookCardSkeleton
        v-for="i in 6"
        :key="i"
      />
    </div>

    <!-- Erreur -->
    <ErrorAlert
      v-else-if="error"
      :message="error"
      show-retry
      @retry="loadData"
    />

    <!-- Liste des livres -->
    <div v-else-if="filteredBooks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <BookCard
        v-for="livre in filteredBooks"
        :key="livre.id"
        :book="livre"
        :can-edit="canEditBook(livre)"
        @view-details="handleViewDetails"
        @edit="openEditModal"
        @delete="openDeleteModal"
      />
    </div>

    <!-- Aucun résultat -->
    <EmptyState
      v-else
      :title="searchQuery || selectedCategory ? 'Aucun livre trouvé' : 'Aucun livre dans la collection'"
      :description="searchQuery || selectedCategory ? 'Essayez de modifier vos critères de recherche.' : 'La collection est vide pour le moment.'"
      :show-action="!searchQuery && !selectedCategory"
      action-text="Ajouter votre premier livre"
      action-button-class="btn-primary"
      :show-clear-filters="!!(searchQuery || selectedCategory)"
      @action="openAddModal"
      @clear-filters="clearFilters"
    />

    <!-- Modal d'ajout/modification -->
    <BookModal
      :is-open="showModal"
      :book="currentBook"
      @close="closeModal"
      @success="handleModalSuccess"
    />

    <!-- Modal de confirmation de suppression -->
    <div class="modal" :class="{ 'modal-open': showDeleteModal }">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Confirmer la suppression</h3>
        <p class="py-4">
          Êtes-vous sûr de vouloir supprimer le livre "{{ bookToDelete?.titre }}" ?
          Cette action est irréversible.
        </p>
        <div class="modal-action">
          <button class="btn btn-ghost" @click="cancelDelete" :disabled="deleteLoading">
            Annuler
          </button>
          <button class="btn btn-error" @click="confirmDelete" :disabled="deleteLoading">
            <span v-if="deleteLoading" class="loading loading-spinner loading-sm"></span>
            Supprimer
          </button>
        </div>
      </div>
      <div class="modal-backdrop" @click="cancelDelete"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { getBooks, getCategories } = useApi()
const { user, isAuthenticated, isAdmin } = useAuth()

// Meta
useHead({
  title: 'Livres - Bibliothèque',
  meta: [
    {
      name: 'description',
      content: 'Explorez notre catalogue de livres'
    }
  ]
})



// État 
const books = ref<any[]>([])
const categories = ref<any[]>([])
const pending = ref(true)
const error = ref<string | null>(null)
const searchQuery = ref('')
const selectedCategory = ref('')

// État des modals
const showModal = ref(false)
const currentBook = ref<any>(null)
const showDeleteModal = ref(false)
const bookToDelete = ref<any>(null)
const deleteLoading = ref(false)

// Gérer les détails d'un livre
const handleViewDetails = (book: any) => {
  console.log('Voir détails du livre:', book)
  // Ici on pourrait naviguer vers une page de détail ou ouvrir une modal
}

// Vérifier si l'utilisateur peut modifier/supprimer un livre
const canEditBook = (book: any) => {
  if (!isAuthenticated.value) return false
  if (isAdmin.value) return true
  return book.proprietaire?.id === user.value?.id
}

// Gestion des modals
const openAddModal = () => {
  currentBook.value = null
  showModal.value = true
}

const openEditModal = (book: any) => {
  currentBook.value = book
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  currentBook.value = null
}

const handleModalSuccess = () => {
  loadData() // Recharger les données
}

// Gestion de la suppression
const openDeleteModal = (book: any) => {
  bookToDelete.value = book
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  bookToDelete.value = null
}

const confirmDelete = async () => {
  if (!bookToDelete.value) return

  deleteLoading.value = true
  try {
    await $fetch(`http://localhost:8000/api/livres/${bookToDelete.value.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${await getToken()}`
      }
    })
    
    // Retirer le livre de la liste local
    books.value = books.value.filter(book => book.id !== bookToDelete.value.id)
    
    cancelDelete()
  } catch (err: any) {
    console.error('Erreur suppression livre:', err)
    // Ici on pourrait afficher une notification d'erreur
  } finally {
    deleteLoading.value = false
  }
}

// Fonction utilitaire pour obtenir le token
const getToken = async () => {
  // Cette fonction devrait être accessible depuis useAuth
  return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token')
}

// Livres filtrés
const filteredBooks = computed(() => {
  let filtered = books.value

  // Filtre par recherche textuelle
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(livre => 
      livre.titre?.toLowerCase().includes(query) ||
      livre.isbn?.toLowerCase().includes(query) ||
      livre.resume?.toLowerCase().includes(query) ||
      (livre.auteur && `${livre.auteur.prenom} ${livre.auteur.nom}`.toLowerCase().includes(query))
    )
  }

  // Filtre par catégorie
  if (selectedCategory.value) {
    filtered = filtered.filter(livre => 
      livre.categorie && livre.categorie.id === parseInt(selectedCategory.value)
    )
  }

  return filtered
})

// Effacer les filtres
const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
}

// Charger les données
const loadData = async () => {
  try {
    pending.value = true
    error.value = null
    
    const [booksData, categoriesData] = await Promise.all([
      getBooks(),
      getCategories()
    ])
    
    books.value = booksData
    categories.value = categoriesData
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
</style>
