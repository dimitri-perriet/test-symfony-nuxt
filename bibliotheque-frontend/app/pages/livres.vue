<template>
  <div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="hero bg-base-200 rounded-box mb-8">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <h1 class="text-5xl font-bold">Catalogue des livres</h1>
          <p class="py-6">
            {{ books.length }} {{ books.length <= 1 ? 'livre' : 'livres' }} dans notre collection
          </p>
        </div>
      </div>
    </div>

    <!-- Barre de recherche et filtres -->
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
                placeholder="Rechercher par titre, auteur, ISBN..." 
              />
            </label>
          </div>

          <!-- Filtre par catégorie -->
          <div class="lg:w-64">
            <select v-model="selectedCategory" class="select select-bordered w-full">
              <option value="">Toutes les catégories</option>
              <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                {{ categorie.nom }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Chargement avec skeletons -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <BookCardSkeleton
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

    <!-- Liste des livres -->
    <div v-else-if="filteredBooks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <BookCard
        v-for="livre in filteredBooks"
        :key="livre.id"
        :book="livre"
        @view-details="handleViewDetails"
      />
    </div>

    <!-- Aucun résultat -->
    <div v-else class="hero min-h-96">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <div class="w-24 h-24 mx-auto mb-4 opacity-20">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold">
            {{ searchQuery || selectedCategory ? 'Aucun livre trouvé' : 'Aucun livre dans la collection' }}
          </h1>
          <p class="py-6 opacity-70">
            {{ searchQuery || selectedCategory 
              ? 'Essayez de modifier vos critères de recherche.' 
              : 'La collection est vide pour le moment.' 
            }}
          </p>
          <button 
            v-if="searchQuery || selectedCategory" 
            @click="clearFilters"
            class="btn btn-primary"
          >
            Effacer les filtres
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { getBooks, getCategories } = useApi()

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

// Gérer les détails d'un livre
const handleViewDetails = (book: any) => {
  console.log('Voir détails du livre:', book)
  // Ici on pourrait naviguer vers une page de détail ou ouvrir une modal
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
