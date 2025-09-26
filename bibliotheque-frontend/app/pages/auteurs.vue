<template>
  <div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="hero bg-base-200 rounded-box mb-8">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <h1 class="text-5xl font-bold">Auteurs</h1>
          <p class="py-6">
            {{ authors.length }} {{ authors.length <= 1 ? 'auteur' : 'auteurs' }} dans notre collection
          </p>
        </div>
      </div>
    </div>

    <!-- Barre de recherche -->
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
                placeholder="Rechercher par nom, prénom..." 
              />
            </label>
          </div>

          <!-- Tri -->
          <div class="lg:w-64">
            <select v-model="sortBy" class="select select-bordered w-full">
              <option value="nom">Trier par nom</option>
              <option value="prenom">Trier par prénom</option>
              <option value="naissance">Trier par date de naissance</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Chargement avec skeletons -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <AuthorCardSkeleton
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

    <!-- Liste des auteurs -->
    <div v-else-if="filteredAuthors.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <AuthorCard
        v-for="auteur in filteredAuthors"
        :key="auteur.id"
        :author="auteur"
        :books-count="getAuthorBooksCount(auteur.id)"
        @view-books="showAuthorBooks"
      />
    </div>

    <!-- Aucun résultat -->
    <div v-else class="hero min-h-96">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <div class="w-24 h-24 mx-auto mb-4 opacity-20">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold">
            {{ searchQuery ? 'Aucun auteur trouvé' : 'Aucun auteur dans la collection' }}
          </h1>
          <p class="py-6 opacity-70">
            {{ searchQuery 
              ? 'Essayez de modifier vos critères de recherche.' 
              : 'La collection d\'auteurs est vide pour le moment.' 
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

    <!-- Modal pour afficher les livres d'un auteur -->
    <dialog ref="booksModal" class="modal">
      <div class="modal-box w-11/12 max-w-4xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-lg">
            Livres de {{ selectedAuthor?.prenom }} {{ selectedAuthor?.nom }}
          </h3>
          <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost">✕</button>
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
  </div>
</template>

<script setup lang="ts">
const { getAuthors, getBooks } = useApi()

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

// Fermer la modal
const closeModal = () => {
  booksModal.value?.close()
  selectedAuthor.value = null
  authorBooks.value = []
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
