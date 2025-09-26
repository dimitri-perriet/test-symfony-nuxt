<template>
  <div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="hero bg-base-200 rounded-box mb-8">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <h1 class="text-5xl font-bold">Catégories littéraires</h1>
          <p class="py-6">
            {{ categories.length }} {{ categories.length <= 1 ? 'catégorie' : 'catégories' }} dans notre collection
          </p>
        </div>
      </div>
    </div>

    <!-- Barre de recherche et options -->
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
                v-model="searchQuery"
                type="text" 
                class="grow" 
                placeholder="Rechercher par nom, description..." 
              />
            </label>
          </div>

          <!-- Tri -->
          <div class="lg:w-64">
            <select v-model="sortBy" class="select select-bordered w-full">
              <option value="nom">Trier par nom</option>
              <option value="livres">Trier par nombre de livres</option>
            </select>
          </div>

          <!-- Vue -->
          <div class="join">
            <button 
              @click="viewMode = 'grid'"
              :class="['btn join-item', viewMode === 'grid' ? 'btn-active' : '']"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
              </svg>
            </button>
            <button 
              @click="viewMode = 'list'"
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
    <div v-else-if="error" class="alert alert-error mb-8">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 shrink-0 stroke-current"
        fill="none"
        viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div>
        <h3 class="font-bold">Erreur de chargement</h3>
        <div class="text-xs">{{ error }}</div>
      </div>
    </div>

    <!-- Vue grille -->
    <div v-else-if="filteredCategories.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <CategoryCard
        v-for="categorie in filteredCategories"
        :key="categorie.id"
        :category="categorie"
        :books-count="getCategoryBooksCount(categorie.id)"
        @view-books="showCategoryBooks"
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
      />
    </div>

    <!-- Aucun résultat -->
    <div v-else class="hero min-h-96">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <div class="w-24 h-24 mx-auto mb-4 opacity-20">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold">
            {{ searchQuery ? 'Aucune catégorie trouvée' : 'Aucune catégorie dans la collection' }}
          </h1>
          <p class="py-6 opacity-70">
            {{ searchQuery 
              ? 'Essayez de modifier vos critères de recherche.' 
              : 'La collection de catégories est vide pour le moment.' 
            }}
          </p>
          <button 
            v-if="searchQuery" 
            @click="clearSearch"
            class="btn btn-primary"
          >
            Effacer la recherche
          </button>
        </div>
      </div>
    </div>

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
  </div>
</template>

<script setup lang="ts">
const { getCategories, getBooks } = useApi()

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
const viewMode = ref('grid')
const selectedCategory = ref<any | null>(null)
const categoryBooks = ref<any[]>([])
const booksModal = ref<any>(null)

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
