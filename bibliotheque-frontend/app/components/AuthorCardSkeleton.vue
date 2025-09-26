<template>
  <div class="card bg-base-100 shadow-xl">
    <div class="card-body">
      <!-- Skeleton avatar et nom -->
      <div class="flex items-center gap-4 mb-4">
        <div class="skeleton w-16 h-16 rounded-full shrink-0"></div>
        <div class="flex flex-col gap-2">
          <div class="skeleton h-6 w-32"></div>
          <div class="skeleton h-4 w-20"></div>
        </div>
      </div>

      <!-- Skeleton dates -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="space-y-2">
          <div class="skeleton h-3 w-16"></div>
          <div class="skeleton h-4 w-20"></div>
        </div>
        <div class="space-y-2">
          <div class="skeleton h-3 w-12"></div>
          <div class="skeleton h-4 w-20"></div>
        </div>
      </div>

      <!-- Skeleton biographie -->
      <div class="space-y-2 mb-4">
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-3/4"></div>
      </div>

      <!-- Skeleton nombre de livres -->
      <div class="flex items-center gap-2 mb-4">
        <div class="skeleton h-4 w-4 rounded-full"></div>
        <div class="skeleton h-4 w-16"></div>
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
// Composant skeleton pour AuthorCard
</script>
