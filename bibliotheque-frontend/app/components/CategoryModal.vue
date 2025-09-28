<template>
  <div class="modal" :class="{ 'modal-open': isOpen }">
    <div class="modal-box w-11/12 max-w-2xl">
      <form @submit.prevent="handleSubmit">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-2xl font-bold text-accent">
              {{ isEdit ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
            </h3>
            <p class="text-base-content/70 text-sm mt-1">
              {{ isEdit ? 'Modifiez les informations de la catégorie' : 'Ajoutez une nouvelle catégorie à la base' }}
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
          <!-- Informations principales -->
          <div class="card bg-base-50 shadow-sm">
            <div class="card-body">
              <h4 class="card-title text-lg text-accent mb-4">Informations principales</h4>
              
              <div class="space-y-4">
                <!-- Nom -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">
                      <span class="text-error">*</span> Nom de la catégorie
                    </span>
                  </label>
                  <input
                    v-model="form.nom"
                    type="text"
                    placeholder="Ex: Science-Fiction"
                    class="input input-bordered input-lg"
                    :class="{ 'input-error': !form.nom && submitted }"
                    required
                  />
                  <label v-if="!form.nom && submitted" class="label">
                    <span class="label-text-alt text-error">Le nom est obligatoire</span>
                  </label>
                </div>

                <!-- Description -->
                <div class="form-control flex flex-col">
                  <label class="label">
                    <span class="label-text font-semibold">Description</span>
                  </label>
                  <textarea
                    v-model="form.description"
                    class="textarea textarea-bordered h-28"
                    placeholder="Décrivez cette catégorie littéraire..."
                  ></textarea>
                  <label class="label">
                    <span class="label-text-alt">{{ form.description?.length || 0 }} caractères</span>
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
            class="btn btn-accent"
            :disabled="loading"
          >
            <span v-if="loading" class="loading loading-spinner loading-sm"></span>
            {{ isEdit ? 'Modifier' : 'Créer la catégorie' }}
          </button>
        </div>
      </form>
    </div>
    <div class="modal-backdrop" @click="closeModal"></div>
  </div>
</template>

<script setup lang="ts">
interface Category {
  id?: number
  nom: string
  description?: string
  couleur?: string
}

interface Props {
  isOpen: boolean
  category?: Category | null
  initialData?: { nom: string } | null
}

interface Emits {
  (e: 'close'): void
  (e: 'success', category: Category): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Authentification
const { token } = useAuth()

// État du formulaire
const form = ref({
  nom: '',
  description: ''
})

const loading = ref(false)
const error = ref('')
const submitted = ref(false)


// Computed
const isEdit = computed(() => !!props.category?.id)

// Méthodes
const resetForm = () => {
  form.value = {
    nom: '',
    description: ''
  }
  error.value = ''
  submitted.value = false
}

const loadCategoryData = () => {
  if (props.category) {
    form.value = {
      nom: props.category.nom || '',
      description: props.category.description || ''
    }
  } else if (props.initialData) {
    // Pré-remplir avec les données initiales pour la création
    form.value = {
      nom: props.initialData.nom || '',
      description: ''
    }
  }
}

const handleSubmit = async () => {
  submitted.value = true
  error.value = ''

  // Validation
  if (!form.value.nom.trim()) {
    error.value = 'Le nom de la catégorie est obligatoire'
    return
  }

  loading.value = true

  try {
    const categoryData = {
      nom: form.value.nom.trim(),
      description: form.value.description?.trim() || null
    }

    let result: Category

    if (isEdit.value && props.category?.id) {
      // Modification
      result = await $fetch<Category>(`http://localhost:8000/api/categories/${props.category.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json',
          'Authorization': token.value ? `Bearer ${token.value}` : ''
        },
        body: categoryData
      })
    } else {
      // Création
      result = await $fetch<Category>('http://localhost:8000/api/categories', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json',
          'Authorization': token.value ? `Bearer ${token.value}` : ''
        },
        body: categoryData
      })
    }

    emit('success', result)
    closeModal()
  } catch (err: any) {
    console.error('Erreur sauvegarde catégorie:', err)
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
      loadCategoryData()
    })
  }
})
</script>
