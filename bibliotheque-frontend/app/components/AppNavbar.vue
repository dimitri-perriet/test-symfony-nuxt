<template>
  <div class="navbar bg-base-100 shadow-lg">
    <div class="navbar-start">
      <!-- Menu mobile -->
      <div class="dropdown">
        <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
          </svg>
        </div>
        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
          <li><NuxtLink to="/livres">Livres</NuxtLink></li>
          <li><NuxtLink to="/auteurs">Auteurs</NuxtLink></li>
          <li><NuxtLink to="/categories">Catégories</NuxtLink></li>
        </ul>
      </div>
      
      <!-- Logo -->
      <NuxtLink to="/" class="btn btn-ghost text-xl">
        Bibliothèque
      </NuxtLink>
    </div>
    
    <!-- Menu desktop -->
    <div class="navbar-center hidden lg:flex">
      <ul class="menu menu-horizontal px-1">
        <li><NuxtLink to="/livres" class="hover:text-primary">Livres</NuxtLink></li>
        <li><NuxtLink to="/auteurs" class="hover:text-primary">Auteurs</NuxtLink></li>
        <li><NuxtLink to="/categories" class="hover:text-primary">Catégories</NuxtLink></li>
      </ul>
    </div>
    
    <div class="navbar-end">
      <div class="hidden sm:inline">
        <!-- Toggle Theme -->
        <UiThemeToggle />
      </div>

      <!-- Menu utilisateur connecté -->
      <div v-if="isAuthenticated" class="flex items-center gap-2 ml-4">
        <!-- Dropdown utilisateur -->
        <div class="dropdown dropdown-end">
          <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
            <div class="avatar placeholder">
              <div class="bg-neutral text-neutral-content w-10 rounded-full">
                <span class="text-xs">{{ userInitials }}</span>
              </div>
            </div>
          </div>
          <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
            <li>
              <div class="flex flex-col gap-1 p-2">
                <span class="font-medium">{{ userFullName }}</span>
                <span class="text-xs opacity-60">{{ user?.email }}</span>
              </div>
            </li>
            <div class="divider my-1"></div>
            <li><a>Mon profil (TODO)</a></li>
            <li v-if="isAdmin"><a>Administration (TODO)</a></li>
            <div class="divider my-1"></div>
            <li><a @click="handleLogout" class="text-error">Déconnexion</a></li>
          </ul>
        </div>
      </div>
      
      <!-- Boutons de connexion pour utilisateurs non connectés -->
      <div v-else class="flex gap-2 ml-4">
        <NuxtLink to="/login" class="btn btn-outline btn-xs sm:btn-sm">
          Se connecter
        </NuxtLink>
        <NuxtLink to="/register" class="btn btn-primary btn-xs sm:btn-sm">
          S'inscrire
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { isAuthenticated, user, userFullName, userInitials, isAdmin, logout } = useAuth()

// Gérer la déconnexion
const handleLogout = async () => {
  try {
    await logout()
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}
</script>
