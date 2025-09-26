export default defineNuxtPlugin(() => {
  const { initAuth } = useAuth()
  
  // Initialiser l'authentification côté client
  initAuth()
})
