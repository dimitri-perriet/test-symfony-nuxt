<template>
  <div>
    <!-- Section Héros -->
    <section class="hero min-h-[80vh] bg-gradient-to-br from-primary/20 to-secondary/20">
      <div class="hero-content text-center">
        <div class="max-w-lg">
          <h1 class="text-5xl font-bold mb-6">
            Gérez votre bibliothèque de
            <span class="text-primary">livres</span>
          </h1>
          <p class="text-lg mb-8 opacity-80">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vel nisi quis urna mollis sagittis. Etiam eget ipsum ultrices, aliquam lacus id, aliquet ex.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <NuxtLink to="/livres" class="btn btn-primary btn-lg">
              Parcourir les livres
            </NuxtLink>
            <button class="btn btn-outline btn-lg">
              Créer un compte
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Section Statistiques -->
    <section class="py-16 bg-base-200">
      <div class="container mx-auto px-4">
        <div class="stats shadow w-full">
          <div class="stat">
            <div class="stat-figure text-primary">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="stat-title">Livres disponibles</div>
            <div class="stat-value text-primary">{{ stats.livres }}</div>
            <div class="stat-desc">enregistrés</div>
          </div>
          
          <div class="stat">
            <div class="stat-figure text-secondary">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
            <div class="stat-title">Auteurs</div>
            <div class="stat-value text-secondary">{{ stats.auteurs }}</div>
            <div class="stat-desc">enregistrés</div>
          </div>
          
          <div class="stat">
            <div class="stat-figure text-accent">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
              </svg>
            </div>
            <div class="stat-title">Catégories</div>
            <div class="stat-value text-accent">{{ stats.categories }}</div>
            <div class="stat-desc">enregistrés</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section Livres Récents -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4">Livres récents</h2>
          <p class="text-lg opacity-70">Découvrez les livres les plus récents disponibles</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="livre in livresPopulaires" 
            :key="livre.id" 
            class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow"
          >
            <div class="card-body">
              <h3 class="card-title text-sm">{{ livre.titre }}</h3>
              <p class="text-xs opacity-70">{{ livre.auteur }}</p>
              <p class="text-xs text-primary">{{ livre.categorie }}</p>
              <div class="card-actions justify-end mt-4">
                <button class="btn btn-primary btn-sm">Voir détails</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-8">
          <NuxtLink to="/livres" class="btn btn-outline">
            Voir tous les livres
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Section CTA finale -->
    <section class="py-16 bg-base-200">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Prêt à commencer ?</h2>
        <p class="text-lg opacity-70 mb-8">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vel nisi quis urna mollis sagittis.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button class="btn btn-primary btn-lg">
            S'inscrire gratuitement
          </button>
          <NuxtLink to="/livres" class="btn btn-outline btn-lg">
            Explorer la collection
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
// Configuration de la page
useHead({
  title: 'Accueil - Bibliothèque',
  meta: [
    {
      name: 'description',
      content: 'Découvrez notre vaste collection de livres et rejoignez une communauté passionnée de lecteurs.'
    }
  ]
})

// Données mock pour l'affichage
const stats = ref({
  livres: '1,247',
  auteurs: '342',
  categories: '28'
})

const livresPopulaires = ref([
  {
    id: 1,
    titre: 'Livre 1',
    auteur: 'Auteur 1',
    categorie: 'Catégorie 1'
  },
  {
    id: 2,
    titre: 'Livre 2',
    auteur: 'Auteur 2',
    categorie: 'Catégorie 2'
  },
  {
    id: 3,
    titre: 'Livre 3',
    auteur: 'Auteur 3',
    categorie: 'Catégorie 3'
  },
  {
    id: 4,
    titre: 'Livre 4',
    auteur: 'Auteur 4',
    categorie: 'Catégorie 4'
  }
])

// Chargement des données depuis l'API plus tard
onMounted(async () => {
  // TODO: Récupérer les vraies données depuis l'API
  // const { data: livres } = await $fetch('/api/livres')
})
</script>
