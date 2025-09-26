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
      <!-- Toggle Theme -->
      <UiThemeToggle />
      
      <!-- Boutons de connexion -->
      <div class="flex gap-2 ml-4">
        <button class="btn btn-outline btn-sm">Se connecter</button>
        <button class="btn btn-primary btn-sm">S'inscrire</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Aucune logique nécessaire pour l'instant
</script>
