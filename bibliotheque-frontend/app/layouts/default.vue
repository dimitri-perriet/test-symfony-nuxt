<template>
  <div class="min-h-screen flex flex-col">
    <AppNavbar />
    
    <main class="flex-1">
      <slot />
    </main>
    
    <AppFooter />
    

    <Toast />
  </div>
</template>

<script setup lang="ts">
const { initTheme } = useTheme()

// Initialiser le thème au montage
onMounted(() => {
  initTheme()
})
</script>
