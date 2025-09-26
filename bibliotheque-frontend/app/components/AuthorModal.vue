<template>
  <div class="modal" :class="{ 'modal-open': isOpen }">
    <div class="modal-box w-11/12 max-w-2xl">
      <form @submit.prevent="handleSubmit">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-2xl font-bold text-secondary">
              {{ isEdit ? 'Modifier l\'auteur' : 'Nouvel auteur' }}
            </h3>
            <p class="text-base-content/70 text-sm mt-1">
              {{ isEdit ? 'Modifiez les informations de l\'auteur' : 'Ajoutez un nouvel auteur à la base' }}
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

        <!-- Alert d'erreur -->
        <div v-if="error" class="alert alert-error mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="font-medium">{{ error }}</span>
        </div>

        <!-- Formulaire -->
        <div class="space-y-6">
          <!-- Informations personnelles -->
          <div class="card bg-base-50 shadow-sm">
            <div class="card-body">
              <h4 class="card-title text-lg text-secondary mb-4">Informations personnelles</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Prénom -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">
                      <span class="text-error">*</span> Prénom
                    </span>
                  </label>
                  <input
                    v-model="form.prenom"
                    type="text"
                    placeholder="Ex: J.R.R."
                    class="input input-bordered"
                    :class="{ 'input-error': !form.prenom && submitted }"
                    required
                  />
                  <label v-if="!form.prenom && submitted" class="label">
                    <span class="label-text-alt text-error">Le prénom est obligatoire</span>
                  </label>
                </div>

                <!-- Nom -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">
                      <span class="text-error">*</span> Nom
                    </span>
                  </label>
                  <input
                    v-model="form.nom"
                    type="text"
                    placeholder="Ex: Tolkien"
                    class="input input-bordered"
                    :class="{ 'input-error': !form.nom && submitted }"
                    required
                  />
                  <label v-if="!form.nom && submitted" class="label">
                    <span class="label-text-alt text-error">Le nom est obligatoire</span>
                  </label>
                </div>
              </div>

              <!-- Nom complet (preview) -->
              <div v-if="form.prenom || form.nom" class="mt-4">
                <div class="alert alert-info">
                  <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Nom complet : <strong>{{ form.prenom }} {{ form.nom }}</strong></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Informations complémentaires -->
          <div class="card bg-base-50 shadow-sm">
            <div class="card-body">
              <h4 class="card-title text-lg text-info mb-4">Informations complémentaires</h4>
              
              <div class="space-y-4">
                <!-- Biographie -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">Biographie</span>
                  </label>
                  <textarea
                    v-model="form.biographie"
                    class="textarea textarea-bordered h-28"
                    placeholder="Courte biographie de l'auteur..."
                  ></textarea>
                  <label class="label">
                    <span class="label-text-alt">{{ form.biographie?.length || 0 }} caractères</span>
                  </label>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Date de naissance -->
                  <div class="form-control flex flex-col">
                    <label class="label">
                      <span class="label-text font-semibold">Date de naissance</span>
                    </label>
                    
                    <!-- Dropdown calendrier -->
                    <div class="dropdown dropdown-top dropdown-end w-full">
                      <div tabindex="0" role="button" class="btn btn-outline w-full justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatBirthDate() || 'Choisir une date' }}
                      </div>
                      
                      <div tabindex="0" class="dropdown-content z-[100] bg-base-100 rounded-box shadow-xl border border-base-300 p-4 mt-2">
                        <!-- Calendrier Cally -->
                        <calendar-date 
                          class="cally bg-base-100"
                          :value="form.dateNaissance"
                          @change="onBirthDateChange"
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
                            @click="clearBirthDate"
                          >
                            Effacer
                          </button>
                          <button 
                            type="button" 
                            class="btn btn-primary btn-sm"
                            @click="setTodayBirth"
                          >
                            Aujourd'hui
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Date de décès -->
                  <div class="form-control flex flex-col">
                    <label class="label">
                      <span class="label-text font-semibold">Date de décès</span>
                    </label>
                    
                    <!-- Dropdown calendrier -->
                    <div class="dropdown dropdown-top dropdown-end w-full">
                      <div tabindex="0" role="button" class="btn btn-outline w-full justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatDeathDate() || 'Choisir une date' }}
                      </div>
                      
                      <div tabindex="0" class="dropdown-content z-[100] bg-base-100 rounded-box shadow-xl border border-base-300 p-4 mt-2">
                        <!-- Message si pas de date de naissance -->
                        <div v-if="!form.dateNaissance" class="alert alert-warning">
                          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L5.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                          </svg>
                          <span>Veuillez d'abord sélectionner une date de naissance</span>
                        </div>
                        
                        <!-- Calendrier Cally -->
                        <calendar-date 
                          v-else
                          class="cally bg-base-100"
                          :value="form.dateDeces"
                          @change="onDeathDateChange"
                          :min="form.dateNaissance"
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
                            @click="clearDeathDate"
                          >
                            Effacer
                          </button>
                          <button 
                            type="button" 
                            class="btn btn-primary btn-sm"
                            @click="setTodayDeath"
                          >
                            Aujourd'hui
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <label v-if="!form.dateNaissance" class="label">
                      <span class="label-text-alt">Saisissez d'abord la date de naissance</span>
                    </label>
                  </div>
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
            class="btn btn-secondary"
            :disabled="loading"
          >
            <span v-if="loading" class="loading loading-spinner loading-sm"></span>
            {{ isEdit ? 'Modifier' : 'Créer l\'auteur' }}
          </button>
        </div>
      </form>
    </div>
    <div class="modal-backdrop" @click="closeModal"></div>
  </div>
</template>

<script setup lang="ts">
interface Author {
  id?: number
  prenom: string
  nom: string
  biographie?: string
  dateNaissance?: string
  dateDeces?: string
}

interface Props {
  isOpen: boolean
  author?: Author | null
  initialData?: { prenom: string; nom: string } | null
}

interface Emits {
  (e: 'close'): void
  (e: 'success', author: Author): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Authentification
const { token } = useAuth()

// État du formulaire
const form = ref({
  prenom: '',
  nom: '',
  biographie: '',
  dateNaissance: '',
  dateDeces: ''
})

const loading = ref(false)
const error = ref('')
const submitted = ref(false)

// État pour le calendrier
const calendarLoaded = ref(false)

// Computed
const isEdit = computed(() => !!props.author?.id)

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

// Gestion du calendrier
const onBirthDateChange = (event: any) => {
  form.value.dateNaissance = event.target.value
}

const onDeathDateChange = (event: any) => {
  form.value.dateDeces = event.target.value
}

const clearBirthDate = () => {
  form.value.dateNaissance = ''
}

const clearDeathDate = () => {
  form.value.dateDeces = ''
}

// Fonctions de formatage des dates
const formatBirthDate = () => {
  if (form.value.dateNaissance) {
    const date = new Date(form.value.dateNaissance)
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }
  return null
}

const formatDeathDate = () => {
  if (form.value.dateDeces) {
    const date = new Date(form.value.dateDeces)
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }
  return null
}

// Fonctions pour définir la date à aujourd'hui
const setTodayBirth = () => {
  const today = new Date()
  form.value.dateNaissance = today.toISOString().split('T')[0] || ''
}

const setTodayDeath = () => {
  const today = new Date()
  form.value.dateDeces = today.toISOString().split('T')[0] || ''
}

// Méthodes
const resetForm = () => {
  form.value = {
    prenom: '',
    nom: '',
    biographie: '',
    dateNaissance: '',
    dateDeces: ''
  }
  error.value = ''
  submitted.value = false
}

const loadAuthorData = () => {
  if (props.author) {
    form.value = {
      prenom: props.author.prenom || '',
      nom: props.author.nom || '',
      biographie: props.author.biographie || '',
      dateNaissance: props.author.dateNaissance || '',
      dateDeces: props.author.dateDeces || ''
    }
  } else if (props.initialData) {
    // Pré-remplir avec les données initiales pour la création
    form.value = {
      prenom: props.initialData.prenom || '',
      nom: props.initialData.nom || '',
      biographie: '',
      dateNaissance: '',
      dateDeces: ''
    }
  }
}

const handleSubmit = async () => {
  submitted.value = true
  error.value = ''

  // Validation
  if (!form.value.prenom.trim() || !form.value.nom.trim()) {
    error.value = 'Le prénom et le nom sont obligatoires'
    return
  }

  loading.value = true

  try {
    const authorData = {
      prenom: form.value.prenom.trim(),
      nom: form.value.nom.trim(),
      biographie: form.value.biographie?.trim() || null,
      dateNaissance: form.value.dateNaissance || null,
      dateDeces: form.value.dateDeces || null
    }

    let result: Author

    if (isEdit.value && props.author?.id) {
      // Modification
      result = await $fetch<Author>(`http://localhost:8000/api/auteurs/${props.author.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json',
          'Authorization': token.value ? `Bearer ${token.value}` : ''
        },
        body: authorData
      })
    } else {
      // Création
      result = await $fetch<Author>('http://localhost:8000/api/auteurs', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json',
          'Authorization': token.value ? `Bearer ${token.value}` : ''
        },
        body: authorData
      })
    }

    emit('success', result)
    closeModal()
  } catch (err: any) {
    console.error('Erreur sauvegarde auteur:', err)
    error.value = err.data?.detail || 'Erreur lors de la sauvegarde'
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  resetForm()
  emit('close')
}

// Watchers
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    resetForm()
    nextTick(() => {
      loadAuthorData()
      if (!calendarLoaded.value) {
        loadCallyComponent()
      }
    })
  }
})
</script>
