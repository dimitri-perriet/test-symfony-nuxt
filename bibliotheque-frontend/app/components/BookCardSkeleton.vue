<template>
  <div class="card bg-base-100 shadow-xl">
    <div class="card-body">
      <!-- Skeleton titre -->
      <div class="skeleton h-6 w-3/4 mb-4"></div>
      
      <!-- Skeleton auteur -->
      <div class="flex items-center mb-2">
        <div class="skeleton h-4 w-4 rounded-full mr-2"></div>
        <div class="skeleton h-4 w-1/2"></div>
      </div>

      <!-- Skeleton catégorie -->
      <div class="mb-3">
        <div class="skeleton h-6 w-20 rounded-full"></div>
      </div>

      <!-- Skeleton résumé -->
      <div class="space-y-2 mb-3">
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-2/3"></div>
      </div>

      <!-- Skeleton métadonnées -->
      <div class="space-y-1 mb-4">
        <div class="skeleton h-3 w-1/3"></div>
        <div class="skeleton h-3 w-1/2"></div>
      </div>

      <!-- Skeleton actions -->
      <div class="flex justify-between items-center">
        <div class="skeleton h-8 w-24"></div>
        <div class="skeleton h-5 w-12"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Composant skeleton pour BookCard
</script>
