<template>
  <div class="min-h-screen hero bg-base-200">
    <div class="hero-content flex-col lg:flex-row">
      <div class="text-center lg:text-left">
        <h1 class="text-5xl font-bold">Inscription</h1>
        <p class="py-6">
          Créez votre compte pour commencer à gérer la bibliothèque.
        </p>
        
      </div>
      
      <!-- Formulaire d'inscription -->
      <div class="card flex-shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
        <form @submit.prevent="handleRegister" class="card-body">
          <h2 class="card-title justify-center mb-4">Créer un compte</h2>
          
          <!-- Alert erreur -->
          <div v-if="error" class="alert alert-error mb-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="stroke-current shrink-0 h-6 w-6"
              fill="none"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ error }}</span>
          </div>
          
          <!-- Alert succès -->
          <div v-if="success" class="alert alert-success mb-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="stroke-current shrink-0 h-6 w-6"
              fill="none"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ success }}</span>
          </div>
          
          <!-- Champs nom et prénom -->
          <div class="grid grid-cols-2 gap-2">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Prénom</span>
              </label>
              <input
                v-model="form.prenom"
                type="text"
                placeholder="Prénom"
                class="input input-bordered"
                required
                :disabled="loading"
              />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Nom</span>
              </label>
              <input
                v-model="form.nom"
                type="text"
                placeholder="Nom"
                class="input input-bordered"
                required
                :disabled="loading"
              />
            </div>
          </div>
          
          <!-- Champ email -->
          <div class="form-control">
            <label class="label">
              <span class="label-text">Email</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="h-4 w-4 opacity-70">
                <path
                  d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" />
                <path
                  d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" />
              </svg>
              <input
                v-model="form.email"
                type="email"
                class="grow"
                placeholder="votre-email@exemple.com"
                required
                :disabled="loading"
              />
            </label>
          </div>
          
          <!-- Champ mot de passe -->
          <div class="form-control">
            <label class="label">
              <span class="label-text">Mot de passe</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="h-4 w-4 opacity-70">
                <path
                  fill-rule="evenodd"
                  d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z"
                  clip-rule="evenodd" />
              </svg>
              <input
                v-model="form.password"
                type="password"
                class="grow"
                placeholder="Minimum 6 caractères"
                required
                :disabled="loading"
              />
            </label>
          </div>
          
          <!-- Confirmation mot de passe -->
          <div class="form-control">
            <label class="label">
              <span class="label-text">Confirmer le mot de passe</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="h-4 w-4 opacity-70">
                <path
                  fill-rule="evenodd"
                  d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z"
                  clip-rule="evenodd" />
              </svg>
              <input
                v-model="form.confirmPassword"
                type="password"
                class="grow"
                placeholder="Répétez votre mot de passe"
                required
                :disabled="loading"
              />
            </label>
          </div>
          
          <!-- Indicateur de force du mot de passe -->
          <div v-if="form.password" class="form-control">
            <div class="flex items-center gap-2">
              <span class="text-xs">Force</span>
              <progress
                class="progress w-full"
                :class="passwordStrengthClass"
                :value="passwordStrength"
                max="100"
              ></progress>
              <span class="text-xs" :class="passwordStrengthTextClass">
                {{ passwordStrengthText }}
              </span>
            </div>
          </div>
          

          
          <!-- Bouton d'inscription -->
          <div class="form-control mt-6">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="loading || !isFormValid"
            >
              <span v-if="loading" class="loading loading-spinner loading-sm"></span>
              Créer mon compte
            </button>
          </div>
          
          <!-- Divider -->
          <div class="divider">ou</div>
          
          <!-- Lien vers connexion -->
          <div class="text-center">
            <p class="text-sm">
              Déjà un compte ?
              <NuxtLink to="/login" class="link link-primary font-medium">
                Se connecter
              </NuxtLink>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Middleware pour les utilisateurs non connectés uniquement
definePageMeta({
  middleware: 'guest'
})

const { register } = useAuth()

// Meta
useHead({
  title: 'Inscription - Bibliothèque',
  meta: [
    {
      name: 'description',
      content: 'Créez votre compte'
    }
  ]
})

// État
const form = reactive({
  prenom: '',
  nom: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const loading = ref(false)
const error = ref<string | null>(null)
const success = ref<string | null>(null)

// Validation du formulaire
const isFormValid = computed(() => {
  return form.prenom && 
         form.nom && 
         form.email && 
         form.password && 
         form.confirmPassword && 
         form.password === form.confirmPassword &&
         form.password.length >= 6 &&
         isValidEmail(form.email)
})

// Force du mot de passe
const passwordStrength = computed(() => {
  const password = form.password
  if (!password) return 0
  
  let strength = 0
  if (password.length >= 6) strength += 25
  if (password.length >= 8) strength += 25
  if (/[A-Z]/.test(password)) strength += 25
  if (/[0-9]/.test(password)) strength += 25
  
  return strength
})

const passwordStrengthText = computed(() => {
  const strength = passwordStrength.value
  if (strength < 25) return 'Faible'
  if (strength < 50) return 'Moyen'
  if (strength < 75) return 'Bon'
  return 'Excellent'
})

const passwordStrengthClass = computed(() => {
  const strength = passwordStrength.value
  if (strength < 25) return 'progress-error'
  if (strength < 50) return 'progress-warning'
  if (strength < 75) return 'progress-info'
  return 'progress-success'
})

const passwordStrengthTextClass = computed(() => {
  const strength = passwordStrength.value
  if (strength < 25) return 'text-error'
  if (strength < 50) return 'text-warning'
  if (strength < 75) return 'text-info'
  return 'text-success'
})

// Gérer la soumission du formulaire
const handleRegister = async () => {
  loading.value = true
  error.value = null
  success.value = null

  try {
    // Validation
    if (!isFormValid.value) {
      throw new Error('Veuillez remplir correctement tous les champs')
    }

    if (form.password !== form.confirmPassword) {
      throw new Error('Les mots de passe ne correspondent pas')
    }

    if (form.password.length < 6) {
      throw new Error('Le mot de passe doit contenir au moins 6 caractères')
    }

    if (!isValidEmail(form.email)) {
      throw new Error('Veuillez saisir une adresse email valide')
    }

    // Tentative d'inscription
    const user = await register({
      prenom: form.prenom,
      nom: form.nom,
      email: form.email,
      plainPassword: form.password
    })
    
    success.value = 'Compte créé avec succès ! Connexion automatique en cours...'
    
    // Redirection après un délai vers la page d'accueil
    setTimeout(() => {
      navigateTo('/')
    }, 1500)

  } catch (err: any) {
    error.value = err.message || 'Une erreur est survenue lors de l\'inscription'
  } finally {
    loading.value = false
  }
}

// Validation email simple
const isValidEmail = (email: string) => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

onMounted(() => {
  
})

</script>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
