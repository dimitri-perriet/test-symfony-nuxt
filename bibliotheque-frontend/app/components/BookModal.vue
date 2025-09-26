<template>
  <div class="modal" :class="{ 'modal-open': isOpen }">
    <div class="modal-box w-11/12 max-w-5xl max-h-[90vh] overflow-y-auto">
      <form @submit.prevent="handleSubmit">
        <!-- Header -->
        <div class="sticky top-0 bg-base-100 z-10 pb-4 mb-6 border-b border-base-300">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-2xl font-bold text-primary">
                {{ isEdit ? 'Modifier le livre' : 'Ajouter un livre' }}
              </h3>
              <p class="text-base-content/70 text-sm mt-1">
                {{ isEdit ? 'Modifiez les informations de votre livre' : 'Ajoutez un nouveau livre à votre collection' }}
              </p>
            </div>
            <button
              type="button"
              class="btn btn-sm btn-circle btn-ghost hover:btn-error"
              @click="closeModal"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Alert d'erreur -->
        <div v-if="error" class="alert alert-error mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="font-medium">{{ error }}</span>
        </div>

        <!-- Formulaire-->
        <div class="space-y-6">
          <!-- Section 1: Informations principales -->
          <div class="card bg-base-50 shadow-sm">
            <div class="card-body">
              <h4 class="card-title text-lg text-primary mb-4">Informations principales</h4>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Titre -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">
                      <span class="text-error">*</span> Titre du livre
                    </span>
                  </label>
                  <input
                    v-model="form.titre"
                    type="text"
                    placeholder="Ex: Le Seigneur des Anneaux"
                    class="input input-bordered input-lg"
                    :class="{ 'input-error': !form.titre && submitted }"
                    required
                  />
                  <label v-if="!form.titre && submitted" class="label">
                    <span class="label-text-alt text-error">Le titre est obligatoire</span>
                  </label>
                </div>

                <!-- Résumé -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">Résumé</span>
                  </label>
                  <textarea
                    v-model="form.resume"
                    class="textarea textarea-bordered h-32"
                    placeholder="Décrivez brièvement l'histoire, les thèmes principaux..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Auteur et Catégorie -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Auteur avec auto-complétion -->
            <div class="card bg-base-50 shadow-sm" :class="{ 'ring-2 ring-error': !selectedAuthor && submitted }">
              <div class="card-body">
                <h4 class="card-title text-lg text-secondary mb-4">
                  <span class="text-error">*</span> Auteur
                </h4>
                
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">
                      <span class="text-error">*</span> Nom de l'auteur
                    </span>
                  </label>
                  <div class="dropdown w-full" :class="{ 'dropdown-open': authorQuery.length > 0 && !selectedAuthor }">
                    <input
                      v-model="authorQuery"
                      type="text"
                      placeholder="Ex: J.R.R. Tolkien"
                      class="input input-bordered w-full"
                      :class="{ 'input-error': !selectedAuthor && submitted }"
                      @input="debouncedSearchAuthors"
                      @focus="debouncedSearchAuthors"
                      required
                    />
                    
                    <!-- Suggestions dropdown -->
                    <ul v-if="authorQuery.length > 0 && !selectedAuthor" 
                        class="dropdown-content z-[100] menu p-2 shadow-lg bg-base-100 rounded-box w-full max-h-60 overflow-y-auto border border-base-300">
                      <li v-for="author in authorSuggestions" :key="author.id" @click="selectAuthor(author)">
                        <a class="flex items-center gap-2">
                          <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-8">
                              <span class="text-xs">{{ author.prenom[0] }}{{ author.nom[0] }}</span>
                            </div>
                          </div>
                          <span>{{ author.prenom }} {{ author.nom }}</span>
                        </a>
                      </li>
                      <!-- Toujours proposer la création si la recherche est assez longue -->
                      <li v-if="authorQuery.length > 1 && !authorSuggestions.some(a => `${a.prenom} ${a.nom}`.toLowerCase() === authorQuery.toLowerCase())" 
                          @click="openAuthorModal">
                        <a class="text-primary font-medium">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                          </svg>
                          Créer "{{ authorQuery }}"
                        </a>
                      </li>
                      
                      <!-- Message si aucun résultat et recherche trop courte -->
                      <li v-if="authorQuery.length > 0 && authorQuery.length <= 1" class="disabled">
                        <a class="text-base-content/50">
                          Tapez au moins 2 caractères pour rechercher...
                        </a>
                      </li>
                      
                    </ul>
                  </div>
                  
                  <!-- Auteur sélectionné -->
                  <div v-if="selectedAuthor" class="mt-3">
                    <div class="alert alert-success">
                      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ selectedAuthor.prenom }} {{ selectedAuthor.nom }}</span>
                      <div class="flex gap-1">
                        <button type="button" class="btn btn-sm btn-circle btn-ghost" @click="editAuthor" title="Modifier l'auteur">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button type="button" class="btn btn-sm btn-circle btn-ghost" @click="clearAuthor" title="Supprimer la sélection">✕</button>
                      </div>
                    </div>
                  </div>
                  
                  <label v-if="!selectedAuthor && submitted" class="label">
                    <span class="label-text-alt text-error">L'auteur est obligatoire</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Catégorie avec auto-complétion -->
            <div class="card bg-base-50 shadow-sm" :class="{ 'ring-2 ring-error': !selectedCategory && submitted }">
              <div class="card-body">
                <h4 class="card-title text-lg text-accent mb-4">
                  <span class="text-error">*</span> Catégorie
                </h4>
                
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">
                      <span class="text-error">*</span> Genre littéraire
                    </span>
                  </label>
                  <div class="dropdown w-full" :class="{ 'dropdown-open': categoryQuery.length > 0 && !selectedCategory }">
                    <input
                      v-model="categoryQuery"
                      type="text"
                      placeholder="Ex: Science-Fiction"
                      class="input input-bordered w-full"
                      :class="{ 'input-error': !selectedCategory && submitted }"
                      @input="debouncedSearchCategories"
                      @focus="debouncedSearchCategories"
                      required
                    />
                    
                    <!-- Suggestions dropdown -->
                    <ul v-if="categoryQuery.length > 0 && !selectedCategory" 
                        class="dropdown-content z-[100] menu p-2 shadow-lg bg-base-100 rounded-box w-full max-h-60 overflow-y-auto border border-base-300">
                      <li v-for="category in categorySuggestions" :key="category.id" @click="selectCategory(category)">
                        <a class="flex items-center gap-2">
                          <div class="badge badge-outline">{{ category.nom }}</div>
                        </a>
                      </li>
                      <!-- Toujours proposer la création si la recherche est assez longue -->
                      <li v-if="categoryQuery.length > 1 && !categorySuggestions.some(c => c.nom.toLowerCase() === categoryQuery.toLowerCase())" 
                          @click="openCategoryModal">
                        <a class="text-primary font-medium">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                          </svg>
                          Créer "{{ categoryQuery }}"
                        </a>
                      </li>
                      
                      <!-- Message si aucun résultat et recherche trop courte -->
                      <li v-if="categoryQuery.length > 0 && categoryQuery.length <= 1" class="disabled">
                        <a class="text-base-content/50">
                          Tapez au moins 2 caractères pour rechercher...
                        </a>
                      </li>
                      
                    </ul>
                  </div>
                  
                  <!-- Catégorie sélectionnée -->
                  <div v-if="selectedCategory" class="mt-3">
                    <div class="alert alert-info">
                      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ selectedCategory.nom }}</span>
                      <div class="flex gap-1">
                        <button type="button" class="btn btn-sm btn-circle btn-ghost" @click="editCategory" title="Modifier la catégorie">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button type="button" class="btn btn-sm btn-circle btn-ghost" @click="clearCategory" title="Supprimer la sélection">✕</button>
                      </div>
                    </div>
                  </div>
                  
                  <label v-if="!selectedCategory && submitted" class="label">
                    <span class="label-text-alt text-error">La catégorie est obligatoire</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Détails supplémentaires -->
          <div class="card bg-base-50 shadow-sm">
            <div class="card-body">
              <h4 class="card-title text-lg text-info mb-4">Détails supplémentaires</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- ISBN -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">ISBN</span>
                  </label>
                  <input
                    v-model="form.isbn"
                    type="text"
                    placeholder="978-2-XXXXX-XXX-X"
                    class="input input-bordered"
                    maxlength="17"
                  />
                  <label class="label">
                    <span class="label-text-alt">Identifiant unique du livre (optionnel)</span>
                  </label>
                </div>

                <!-- Date de publication avec calendrier DaisyUI -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">Date de publication</span>
                  </label>
                  <div class="dropdown dropdown-top dropdown-end w-full">
                    <div tabindex="0" role="button" class="btn btn-outline w-full justify-start">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      {{ formatDisplayDate() || 'Choisir une date' }}
                    </div>
                    <div tabindex="0" class="dropdown-content z-[100] bg-base-100 rounded-box shadow-xl border border-base-300 p-4 mb-2">
                      <!-- Calendrier Cally -->
                      <calendar-date 
                        class="cally bg-base-100"
                        :value="form.datePublication"
                        @change="onCalendarChange"
                      >
                        <!-- Bouton précédent -->
                        <svg 
                          aria-label="Mois précédent" 
                          class="fill-current size-4 hover:text-primary cursor-pointer" 
                          slot="previous" 
                          xmlns="http://www.w3.org/2000/svg" 
                          viewBox="0 0 24 24"
                        >
                          <path d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        
                        <!-- Bouton suivant -->
                        <svg 
                          aria-label="Mois suivant" 
                          class="fill-current size-4 hover:text-primary cursor-pointer" 
                          slot="next" 
                          xmlns="http://www.w3.org/2000/svg" 
                          viewBox="0 0 24 24"
                        >
                          <path d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                        
                        <!-- Contenu du calendrier -->
                        <calendar-month></calendar-month>
                      </calendar-date>
                      
                      <!-- Actions du calendrier -->
                      <div class="flex justify-between items-center mt-4 pt-4 border-t border-base-300">
                        <button 
                          type="button" 
                          class="btn btn-ghost btn-sm"
                          @click="clearDate"
                        >
                          Effacer
                        </button>
                        <button 
                          type="button" 
                          class="btn btn-primary btn-sm"
                          @click="setToday"
                        >
                          Aujourd'hui
                        </button>
                      </div>
                    </div>
                  </div>
                  <label class="label">
                    <span class="label-text-alt">Date de première publication (optionnel)</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="modal-action">
          <button
            type="button"
            class="btn btn-ghost"
            @click="closeModal"
            :disabled="loading"
          >
            Annuler
          </button>
          <button
            type="submit"
            class="btn btn-primary"
            :disabled="loading"
          >
            <span v-if="loading" class="loading loading-spinner loading-sm"></span>
            {{ isEdit ? 'Modifier' : 'Ajouter' }}
          </button>
        </div>
      </form>
    </div>
    <div class="modal-backdrop" @click="closeModal"></div>
  </div>

  <!-- Modals de création -->
  <AuthorModal
    :is-open="isAuthorModalOpen"
    :author="null"
    :initial-data="newAuthorData"
    @close="closeAuthorModal"
    @success="handleAuthorCreated"
  />
  
  <CategoryModal
    :is-open="isCategoryModalOpen"
    :category="null"
    :initial-data="newCategoryData"
    @close="closeCategoryModal"
    @success="handleCategoryCreated"
  />
</template>

<script setup lang="ts">
interface Author {
  id: number
  nom: string
  prenom: string
}

interface Category {
  id: number
  nom: string
}

interface Book {
  id?: number
  titre: string
  isbn?: string
  datePublication?: string
  resume?: string
  auteur?: Author
  categorie?: Category
}

interface Props {
  isOpen: boolean
  book?: Book | null
}

interface Emits {
  (e: 'close'): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Authentification
const { token } = useAuth()

// État du formulaire
const form = ref({
  titre: '',
  isbn: '',
  datePublication: '',
  resume: ''
})

const selectedAuthor = ref<Author | null>(null)
const selectedCategory = ref<Category | null>(null)
const authorQuery = ref('')
const categoryQuery = ref('')
const authorSuggestions = ref<Author[]>([])
const categorySuggestions = ref<Category[]>([])

// États pour les modals
const isAuthorModalOpen = ref(false)
const isCategoryModalOpen = ref(false)
const newAuthorData = ref<{ prenom: string; nom: string } | null>(null)
const newCategoryData = ref<{ nom: string } | null>(null)

// État pour le calendrier
const calendarLoaded = ref(false)

const loading = ref(false)
const error = ref('')
const submitted = ref(false)

// Computed
const isEdit = computed(() => !!props.book?.id)

// Utilitaires pour la date et calendrier
const formatDisplayDate = () => {
  if (form.value.datePublication) {
    const date = new Date(form.value.datePublication)
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }
  return ''
}

const onCalendarChange = (event: any) => {
  form.value.datePublication = event.target.value
}

const clearDate = () => {
  form.value.datePublication = ''
}

const setToday = () => {
  const today = new Date()
  form.value.datePublication = today.toISOString().split('T')[0] || ''
}

// Charger le composant Cally
const loadCallyComponent = () => {
  if (import.meta.client && !calendarLoaded.value) {
    const script = document.createElement('script')
    script.type = 'module'
    script.src = 'https://unpkg.com/cally'
    script.onload = () => {
      calendarLoaded.value = true
    }
    document.head.appendChild(script)
  }
}

// Méthodes
const resetForm = () => {
  form.value = {
    titre: '',
    isbn: '',
    datePublication: '',
    resume: ''
  }
  selectedAuthor.value = null
  selectedCategory.value = null
  authorQuery.value = ''
  categoryQuery.value = ''
  authorSuggestions.value = []
  categorySuggestions.value = []
  error.value = ''
  submitted.value = false
}

const loadBookData = () => {
  if (props.book) {
    form.value = {
      titre: props.book.titre || '',
      isbn: props.book.isbn || '',
      datePublication: props.book.datePublication || '',
      resume: props.book.resume || ''
    }
    
    if (props.book.auteur) {
      selectedAuthor.value = props.book.auteur
      authorQuery.value = `${props.book.auteur.prenom} ${props.book.auteur.nom}`
    }
    
    if (props.book.categorie) {
      selectedCategory.value = props.book.categorie
      categoryQuery.value = props.book.categorie.nom
    }
  }
}

const searchAuthors = async () => {
  if (authorQuery.value.length < 2) {
    authorSuggestions.value = []
    return
  }

  try {
    const data = await $fetch<{ member: Author[] }>('http://localhost:8000/api/auteurs', {
      headers: {
        'Accept': 'application/ld+json'
      },
      params: {
        'search': authorQuery.value
      }
    })
    
    // Plus besoin de filtrage côté client, le backend fait la recherche
    authorSuggestions.value = data.member.slice(0, 5)
  } catch (err) {
    console.error('Erreur recherche auteurs:', err)
    authorSuggestions.value = []
  }
}

const searchCategories = async () => {
  if (categoryQuery.value.length < 2) {
    categorySuggestions.value = []
    return
  }

  try {
    const data = await $fetch<{ member: Category[] }>('http://localhost:8000/api/categories', {
      headers: {
        'Accept': 'application/ld+json'
      },
      params: {
        'search': categoryQuery.value
      }
    })
    
    // Plus besoin de filtrage côté client, le backend fait la recherche
    categorySuggestions.value = data.member.slice(0, 5)
  } catch (err) {
    console.error('Erreur recherche catégories:', err)
    categorySuggestions.value = []
  }
}

// Fonction debounce simple
const debounce = (func: Function, wait: number) => {
  let timeout: NodeJS.Timeout
  return (...args: any[]) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => func.apply(null, args), wait)
  }
}

// Versions avec debounce
const debouncedSearchAuthors = debounce(searchAuthors, 300)
const debouncedSearchCategories = debounce(searchCategories, 300)

const selectAuthor = (author: Author) => {
  selectedAuthor.value = author
  authorQuery.value = `${author.prenom} ${author.nom}`
  authorSuggestions.value = []
}

const selectCategory = (category: Category) => {
  selectedCategory.value = category
  categoryQuery.value = category.nom
  categorySuggestions.value = []
}

const clearAuthor = () => {
  selectedAuthor.value = null
  authorQuery.value = ''
  authorSuggestions.value = []
}

const clearCategory = () => {
  selectedCategory.value = null
  categoryQuery.value = ''
  categorySuggestions.value = []
}

const editAuthor = () => {
  const currentAuthor = selectedAuthor.value
  selectedAuthor.value = null
  authorQuery.value = currentAuthor ? `${currentAuthor.prenom} ${currentAuthor.nom}` : ''
  // Focus sur l'input pour déclencher la recherche
  nextTick(() => {
    debouncedSearchAuthors()
  })
}

const editCategory = () => {
  const currentCategory = selectedCategory.value
  selectedCategory.value = null
  categoryQuery.value = currentCategory ? currentCategory.nom : ''
  // Focus sur l'input pour déclencher la recherche
  nextTick(() => {
    debouncedSearchCategories()
  })
}

// Fonctions pour ouvrir les modals de création
const openAuthorModal = () => {
  // Stocker les données pour pré-remplir le modal
  const names = authorQuery.value.trim().split(' ')
  newAuthorData.value = { 
    prenom: names[0] || '', 
    nom: names.slice(1).join(' ') || names[0] || '' 
  }
  isAuthorModalOpen.value = true
}

const openCategoryModal = () => {
  // Stocker les données pour pré-remplir le modal
  newCategoryData.value = { nom: categoryQuery.value.trim() }
  isCategoryModalOpen.value = true
}

// Fonctions pour fermer les modals
const closeAuthorModal = () => {
  isAuthorModalOpen.value = false
  newAuthorData.value = null
}

const closeCategoryModal = () => {
  isCategoryModalOpen.value = false
  newCategoryData.value = null
}

// Gestion des succès de création
const handleAuthorCreated = (author: any) => {
  // Sélectionner l'auteur créé
  selectedAuthor.value = author
  authorQuery.value = `${author.prenom} ${author.nom}`
  authorSuggestions.value = []
  
  // Fermer le modal de création
  closeAuthorModal()
}

const handleCategoryCreated = (category: any) => {
  // Sélectionner la catégorie créée
  selectedCategory.value = category
  categoryQuery.value = category.nom
  categorySuggestions.value = []
  
  // Fermer le modal de création
  closeCategoryModal()
}

const handleSubmit = async () => {
  submitted.value = true
  error.value = ''

  // Validation
  if (!form.value.titre.trim()) {
    error.value = 'Le titre est obligatoire'
    return
  }

  if (!selectedAuthor.value) {
    error.value = 'Un auteur doit être sélectionné ou créé'
    return
  }

  if (!selectedCategory.value) {
    error.value = 'Une catégorie doit être sélectionnée ou créée'
    return
  }

  loading.value = true

  try {
    const bookData = {
      titre: form.value.titre,
      isbn: form.value.isbn || null,
      datePublication: form.value.datePublication || null,
      resume: form.value.resume || null,
      auteur: `/api/auteurs/${selectedAuthor.value.id}`,
      categorie: `/api/categories/${selectedCategory.value.id}`
    }

    if (isEdit.value && props.book?.id) {
      // Modification
      await $fetch(`http://localhost:8000/api/livres/${props.book.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json',
          'Authorization': token.value ? `Bearer ${token.value}` : ''
        },
        body: bookData
      })
    } else {
      // Création
      await $fetch('http://localhost:8000/api/livres', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json',
          'Authorization': token.value ? `Bearer ${token.value}` : ''
        },
        body: bookData
      })
    }

    emit('success')
    closeModal()
  } catch (err: any) {
    console.error('Erreur sauvegarde livre:', err)
    error.value = err.data?.detail || 'Erreur lors de la sauvegarde'
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  resetForm()
  emit('close')
}

// Charger Cally au montage
onMounted(() => {
  loadCallyComponent()
})

// Watchers
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    resetForm()
    nextTick(() => {
      loadBookData()
      if (!calendarLoaded.value) {
        loadCallyComponent()
      }
    })
  }
})
</script>
