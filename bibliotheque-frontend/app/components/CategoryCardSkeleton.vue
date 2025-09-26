<template>
  <div class="card bg-base-100 shadow-xl">
    <div class="card-body">
      <!-- Skeleton en-tête avec avatar -->
      <div class="flex items-center gap-4 mb-4">
        <div class="skeleton w-12 h-12 rounded-full shrink-0"></div>
        <div class="flex flex-col gap-2">
          <div class="skeleton h-6 w-24"></div>
          <div class="skeleton h-4 w-16 rounded-full"></div>
        </div>
      </div>

      <!-- Skeleton description -->
      <div class="space-y-2 mb-4">
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-2/3"></div>
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
// Composant skeleton pour CategoryCard
</script>
