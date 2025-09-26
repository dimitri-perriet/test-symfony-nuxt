interface User {
  id: number
  email: string
  nom: string
  prenom: string
  roles: string[]
  createdAt: string
}

interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

  interface RegisterData {
    prenom: string
    nom: string
    email: string
    plainPassword: string
  }

interface AuthResponse {
  token: string
  user: User
}

export const useAuth = () => {
  const { apiCall } = useApi()
  
  // État global de l'authentification
  const user = useState<User | null>('auth.user', () => null)
  const token = useState<string | null>('auth.token', () => null)
  const isAuthenticated = computed(() => !!user.value && !!token.value)

  // Initialiser l'authentification depuis le stockage local
  const initAuth = () => {
    if (import.meta.client) {
      const storedToken = localStorage.getItem('auth_token')
      const storedUser = localStorage.getItem('auth_user')
      
      if (storedToken && storedUser) {
        try {
          token.value = storedToken
          user.value = JSON.parse(storedUser)
        } catch (error) {
          console.error('Erreur lors de la restauration de l\'authentification:', error)
          clearAuth()
        }
      }
    }
  }

  // Sauvegarder l'authentification dans le stockage local
  const saveAuth = (authData: AuthResponse, remember: boolean = false) => {
    token.value = authData.token
    user.value = authData.user

    if (import.meta.client) {
      if (remember) {
        localStorage.setItem('auth_token', authData.token)
        localStorage.setItem('auth_user', JSON.stringify(authData.user))
      } else {
        sessionStorage.setItem('auth_token', authData.token)
        sessionStorage.setItem('auth_user', JSON.stringify(authData.user))
      }
    }
  }

  // Effacer l'authentification
  const clearAuth = () => {
    user.value = null
    token.value = null

    if (import.meta.client) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      sessionStorage.removeItem('auth_token')
      sessionStorage.removeItem('auth_user')
    }
  }

  // Connexion
  const login = async (email: string, password: string, remember: boolean = false) => {
    try {
      // Utiliser l'endpoint /auth pour Lexik JWT
      const response = await $fetch<{ token: string }>('http://localhost:8000/auth', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: { email, password }
      })

      if (!response.token) {
        throw new Error('Token manquant dans la réponse')
      }

      // Récupérer les informations utilisateur avec le token
      const userResponse = await $fetch<User>('http://localhost:8000/api/me', {
        headers: {
          'Authorization': `Bearer ${response.token}`,
          'Accept': 'application/ld+json'
        }
      })

      const authData: AuthResponse = {
        token: response.token,
        user: userResponse
      }

      saveAuth(authData, remember)
      return authData.user

    } catch (error: any) {
      clearAuth()
      
      // Gestion des erreurs spécifiques
      if (error.status === 401 || error.statusCode === 401) {
        throw new Error('Email ou mot de passe incorrect')
      } else if (error.status === 429 || error.statusCode === 429) {
        throw new Error('Trop de tentatives de connexion. Veuillez réessayer plus tard.')
      } else if ((error.status >= 500 && error.status < 600) || (error.statusCode >= 500 && error.statusCode < 600)) {
        throw new Error('Erreur du serveur. Veuillez réessayer plus tard.')
      }
      
      throw new Error(error.message || 'Erreur lors de la connexion')
    }
  }

  // Inscription
  const register = async (userData: RegisterData) => {
    try {
      // Utiliser l'endpoint API Platform pour créer un utilisateur
      // Note a moi meme : L'API retourne null en cas de succès (201)
      await $fetch('http://localhost:8000/api/users', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/ld+json',
          'Accept': 'application/ld+json'
        },
        body: userData
      })

      // Connecter automatiquement l'utilisateur après inscription réussie
      try {
        const connectedUser = await login(userData.email, userData.plainPassword, false)
        return connectedUser
      } catch (loginError) {
        // Si la connexion automatique échoue, l'utilisateur devra se connecter manuellement
        console.warn('Connexion automatique échouée après inscription:', loginError)
        throw new Error('Inscription réussie mais connexion automatique échouée. Veuillez vous connecter manuellement.')
      }

    } catch (error: any) {
      // Gestion des erreurs spécifiques
      if (error.status === 409 || error.statusCode === 409) {
        throw new Error('Cet email est déjà utilisé')
      } else if (error.status === 422 || error.statusCode === 422) {
        throw new Error('Données invalides. Vérifiez vos informations.')
      } else if ((error.status >= 500 && error.status < 600) || (error.statusCode >= 500 && error.statusCode < 600)) {
        throw new Error('Erreur du serveur. Veuillez réessayer plus tard.')
      }
      
      throw new Error(error.message || 'Erreur lors de l\'inscription')
    }
  }

  // Déconnexion
  const logout = async () => {
    try {
      // Avec JWT, pas besoin d'invalider côté serveur le token car il expere naturellement
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error)
    } finally {
      clearAuth()
      await navigateTo('/login')
    }
  }

  // Vérifier et rafraîchir l'authentification
  const checkAuth = async () => {
    if (!token.value) {
      return false
    }

    try {
      const response = await $fetch<User>('http://localhost:8000/api/me', {
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Accept': 'application/ld+json'
        }
      })

      user.value = response
      return true

    } catch (error) {
      console.error('Token invalide ou expiré:', error)
      clearAuth()
      return false
    }
  }

  // Middleware d'authentification pour les routes protégées
  const requireAuth = async () => {
    if (!isAuthenticated.value) {
      await navigateTo('/login')
      return false
    }

    // Vérifier que le token est toujours valide
    const isValid = await checkAuth()
    if (!isValid) {
      await navigateTo('/login')
      return false
    }

    return true
  }

  // Middleware pour les routes guests (login, register)
  const requireGuest = async () => {
    if (isAuthenticated.value) {
      await navigateTo('/')
      return false
    }
    return true
  }

  // Vérifier si l'utilisateur a un rôle spécifique
  const hasRole = (role: string) => {
    return user.value?.roles.includes(role) || false
  }

  // Vérifier si l'utilisateur est admin
  const isAdmin = computed(() => hasRole('ROLE_ADMIN'))

  // Obtenir le nom complet de l'utilisateur
  const userFullName = computed(() => {
    if (!user.value) return ''
    return `${user.value.prenom} ${user.value.nom}`
  })

  // Obtenir les initiales de l'utilisateur
  const userInitials = computed(() => {
    if (!user.value) return ''
    return `${user.value.prenom.charAt(0)}${user.value.nom.charAt(0)}`.toUpperCase()
  })

  return {
    // État
    user: readonly(user),
    token: readonly(token),
    isAuthenticated,
    isAdmin,
    userFullName,
    userInitials,
    
    // Méthodes
    initAuth,
    login,
    register,
    logout,
    checkAuth,
    requireAuth,
    requireGuest,
    hasRole
  }
}
