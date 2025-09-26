<template>
  <div class="min-h-screen hero bg-base-200">
    <div class="hero-content flex-col lg:flex-row-reverse">
      <div class="text-center lg:text-left">
        <h1 class="text-5xl font-bold">Connexion</h1>
        <p class="py-6">
          Connectez-vous à votre compte pour créer des livres.
        </p>
        
      </div>
      
      <!-- Formulaire de connexion -->
      <div class="card flex-shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
        <form @submit.prevent="handleLogin" class="card-body">
          <h2 class="card-title justify-center mb-4">Se connecter</h2>
          
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
                placeholder="Votre mot de passe"
                required
                :disabled="loading"
              />
            </label>
          </div>
          
          <!-- Case à cocher "Se souvenir" -->
          <div class="form-control">
            <label class="label cursor-pointer">
              <span class="label-text">Se souvenir de moi</span>
              <input
                v-model="form.remember"
                type="checkbox"
                class="checkbox checkbox-primary"
                :disabled="loading"
              />
            </label>
          </div>
          
          <!-- Bouton de connexion -->
          <div class="form-control mt-6">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="loading"
            >
              <span v-if="loading" class="loading loading-spinner loading-sm"></span>
              Se connecter
            </button>
          </div>
          
          <!-- Divider -->
          <div class="divider">ou</div>
          
          <!-- Lien vers inscription -->
          <div class="text-center">
            <p class="text-sm">
              Pas encore de compte ?
              <NuxtLink to="/register" class="link link-primary font-medium">
                Créer un compte
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

const { login } = useAuth()

// Meta
useHead({
  title: 'Connexion - Bibliothèque',
  meta: [
    {
      name: 'description',
      content: 'Connectez-vous à votre compte'
    }
  ]
})

// État
const form = reactive({
  email: '',
  password: '',
  remember: false
})

const loading = ref(false)
const error = ref<string | null>(null)
const success = ref<string | null>(null)

// Gérer la soumission du formulaire
const handleLogin = async () => {
  loading.value = true
  error.value = null
  success.value = null

  try {
    // Validation basique
    if (!form.email || !form.password) {
      throw new Error('Veuillez remplir tous les champs obligatoires')
    }

    if (!isValidEmail(form.email)) {
      throw new Error('Veuillez saisir une adresse email valide')
    }

    // Tentative de connexion
    await login(form.email, form.password, form.remember)
    
    success.value = 'Connexion réussie ! Redirection en cours...'
    
    // Redirection après un délai
    setTimeout(() => {
      navigateTo('/')
    }, 1500)

  } catch (err: any) {
    error.value = err.message || 'Une erreur est survenue lors de la connexion'
  } finally {
    loading.value = false
  }
}

// Validation email simple
const isValidEmail = (email: string) => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

// Redirection si déjà connecté
onMounted(() => {
  // Cette logique sera ajoutée quand on aura le composable useAuth
})
</script>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
