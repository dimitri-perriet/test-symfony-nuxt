export default defineNuxtRouteMiddleware(async (to, from) => {
  const { requireAuth } = useAuth()
  
  // Vérifier l'authentification
  const isAuthenticated = await requireAuth()
  
  if (!isAuthenticated) {
    return abortNavigation('Vous devez être connecté pour accéder à cette page')
  }
})
