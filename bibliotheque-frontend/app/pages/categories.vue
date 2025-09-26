<template>
  <div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <PageHeader
      title="Catégories littéraires"
      :count="categories.length"
      singular="catégorie"
      plural="catégories"
      add-button-text="Ajouter une catégorie"
      button-class="btn-accent"
      @add="openAddModal"
    />

    <!-- Barre de recherche et options -->
    <SearchFilters
      v-model:search-query="searchQuery"
      v-model:sort-by="sortBy"
      v-model:view-mode="viewMode"
      search-placeholder="Rechercher par nom, description..."
      :sort-options="[
        { value: 'nom', label: 'Trier par nom' },
        { value: 'livres', label: 'Trier par nombre de livres' }
      ]"
      show-view-toggle
    />

    <!-- Chargement avec skeletons - Vue grille -->
    <div v-if="pending && viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <CategoryCardSkeleton
        v-for="i in 6"
        :key="i"
      />
    </div>

    <!-- Chargement avec skeletons - Vue liste -->
    <div v-if="pending && viewMode === 'list'" class="space-y-4">
      <CategoryListItemSkeleton
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

    <!-- Vue grille -->
    <div v-else-if="filteredCategories.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <CategoryCard
        v-for="categorie in filteredCategories"
        :key="categorie.id"
        :category="categorie"
        :books-count="getCategoryBooksCount(categorie.id)"
        @view-books="showCategoryBooks"
        @edit-category="openEditModal"
        @delete-category="confirmDeleteCategory"
      />
    </div>

    <!-- Vue liste -->
    <div v-else-if="filteredCategories.length > 0 && viewMode === 'list'" class="space-y-4">
      <CategoryListItem
        v-for="categorie in filteredCategories"
        :key="categorie.id"
        :category="categorie"
        :books-count="getCategoryBooksCount(categorie.id)"
        @view-books="showCategoryBooks"
        @edit-category="openEditModal"
        @delete-category="confirmDeleteCategory"
      />
    </div>

    <!-- Aucun résultat -->
    <EmptyState
      v-else
      :title="searchQuery ? 'Aucune catégorie trouvée' : 'Aucune catégorie dans la collection'"
      :description="searchQuery ? 'Essayez de modifier vos critères de recherche.' : 'La collection de catégories est vide pour le moment.'"
      :show-action="!searchQuery"
      action-text="Ajouter votre première catégorie"
      action-button-class="btn-accent"
      :show-clear-filters="!!searchQuery"
      @action="openAddModal"
      @clear-filters="clearSearch"
    >
      <template #icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
        </svg>
      </template>
    </EmptyState>

    <!-- Modal pour afficher les livres d'une catégorie -->
    <dialog ref="booksModal" class="modal">
      <div class="modal-box w-11/12 max-w-5xl">
        <div class="flex justify-between items-center mb-4">
          <div class="flex items-center gap-3">
            <div 
              class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
              :style="{ backgroundColor: selectedCategory?.couleur || '#6B7280' }"
            >
              {{ selectedCategory?.nom?.charAt(0).toUpperCase() }}
            </div>
            <h3 class="font-bold text-lg">
              Livres de la catégorie "{{ selectedCategory?.nom }}"
            </h3>
          </div>
          <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost">✕</button>
        </div>
        
        <div v-if="categoryBooks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="livre in categoryBooks"
            :key="livre.id"
            class="card bg-base-200 shadow-md hover:shadow-lg transition-shadow"
          >
            <div class="card-body p-4">
              <h4 class="card-title text-base">{{ livre.titre }}</h4>
              
              <div v-if="livre.auteur" class="flex items-center gap-2 mb-2">
                <svg class="h-3 w-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-xs opacity-70">{{ livre.auteur.prenom }} {{ livre.auteur.nom }}</span>
              </div>
              
              <p v-if="livre.resume" class="text-sm opacity-70 line-clamp-2 mb-2">
                {{ livre.resume }}
              </p>
              
              <div class="flex justify-between items-center">
                <div v-if="livre.isbn" class="text-xs opacity-60">
                  ISBN: {{ livre.isbn }}
                </div>
                <div class="text-xs opacity-60">
                  {{ livre.datePublication ? formatDate(livre.datePublication) : '' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8 opacity-70">
          Aucun livre trouvé pour cette catégorie.
        </div>
      </div>
      <form method="dialog" class="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>

    <!-- Modal d'ajout/modification de catégorie -->
    <CategoryModal
      :is-open="isCategoryModalOpen"
      :category="selectedCategoryForEdit"
      @close="closeCategoryModal"
      @success="handleCategorySaved"
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
const { getCategories, getBooks } = useApi()
const { isAuthenticated, isAdmin } = useAuth()
const { showSuccess, showError } = useToast()

// Meta
useHead({
  title: 'Catégories - Bibliothèque',
  meta: [
    {
      name: 'description',
      content: 'Explorez nos livres classés par genres et catégories littéraires.'
    }
  ]
})

// État 
const categories = ref<any[]>([])
const books = ref<any[]>([])
const pending = ref(true)
const error = ref<string | null>(null)
const searchQuery = ref('')
const sortBy = ref('nom')
const viewMode = ref<'grid' | 'list'>('grid')
const selectedCategory = ref<any | null>(null)
const categoryBooks = ref<any[]>([])
const booksModal = ref<any>(null)

// État pour le modal d'ajout/modification de catégorie
const isCategoryModalOpen = ref(false)
const selectedCategoryForEdit = ref<any | null>(null)

// Gestion de la modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const categoryToDelete = ref<any | null>(null)

const deleteModalTitle = computed(() => {
  if (!categoryToDelete.value) return ''
  return `Supprimer la catégorie`
})

const deleteModalMessage = computed(() => {
  if (!categoryToDelete.value) return ''
  return `Êtes-vous sûr de vouloir supprimer la catégorie "${categoryToDelete.value.nom}" ?`
})

const deleteModalCascadeWarning = computed(() => {
  if (!categoryToDelete.value) return ''
  const booksCount = getCategoryBooksCount(categoryToDelete.value.id)
  return booksCount > 0 
    ? `Cette action supprimera également ${booksCount} livre(s) associé(s) de façon définitive !`
    : ''
})

// Fonction pour formater les dates
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Compter les livres d'une catégorie
const getCategoryBooksCount = (categoryId: number) => {
  return books.value.filter(livre => livre.categorie?.id === categoryId).length
}

// Catégories filtrées et triées
const filteredCategories = computed(() => {
  let filtered = categories.value

  // Filtre par recherche
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(categorie => 
      categorie.nom?.toLowerCase().includes(query) ||
      categorie.description?.toLowerCase().includes(query)
    )
  }

  // Tri
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'livres':
        return getCategoryBooksCount(b.id) - getCategoryBooksCount(a.id)
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

// Afficher les livres d'une catégorie
const showCategoryBooks = (categorie: any) => {
  selectedCategory.value = categorie
  categoryBooks.value = books.value.filter(livre => livre.categorie?.id === categorie.id)
  booksModal.value?.showModal()
}

// Fermer la modal
const closeModal = () => {
  booksModal.value?.close()
  selectedCategory.value = null
  categoryBooks.value = []
}

// Charger les données
const loadData = async () => {
  try {
    pending.value = true
    error.value = null
    
    const [categoriesData, booksData] = await Promise.all([
      getCategories(),
      getBooks()
    ])
    
    categories.value = categoriesData
    books.value = booksData
  } catch (err) {
    error.value = 'Impossible de charger les données. Veuillez réessayer.'
    console.error('Erreur lors du chargement:', err)
  } finally {
    pending.value = false
  }
}

// Fonctions pour le modal de catégorie
const openAddModal = () => {
  selectedCategoryForEdit.value = null
  isCategoryModalOpen.value = true
}

const openEditModal = (category: any) => {
  selectedCategoryForEdit.value = category
  isCategoryModalOpen.value = true
}

const closeCategoryModal = () => {
  isCategoryModalOpen.value = false
  selectedCategoryForEdit.value = null
}

const handleCategorySaved = (category: any) => {
  // Recharger les données pour voir la nouvelle catégorie
  loadData()
}

// Nouvelle fonction de confirmation de suppression avec modale
const confirmDeleteCategory = (category: any) => {
  categoryToDelete.value = category
  isDeleteModalOpen.value = true
}

// Fonction pour fermer la modale de suppression
const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  categoryToDelete.value = null
  isDeleting.value = false
}

// Fonction de suppression effective
const performDelete = async () => {
  if (!categoryToDelete.value) return
  
  try {
    isDeleting.value = true
    
    await $fetch(`http://localhost:8000/api/categories/${categoryToDelete.value.id}`, {
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
      'Catégorie supprimée', 
      `La catégorie "${categoryToDelete.value.nom}" a été supprimée avec succès`
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

// Charger les données au montage
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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
