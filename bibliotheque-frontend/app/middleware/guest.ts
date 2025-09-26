export default defineNuxtRouteMiddleware(async (to, from) => {
  const { requireGuest } = useAuth()
  
  // Vérifier que l'utilisateur n'est pas connecté
  const canAccess = await requireGuest()
  
  if (!canAccess) {
    return abortNavigation('Vous êtes déjà connecté')
  }
})
