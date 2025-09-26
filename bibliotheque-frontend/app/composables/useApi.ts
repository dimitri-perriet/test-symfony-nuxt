interface ApiOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE'
  headers?: Record<string, string>
  body?: any
}

interface Livre {
  id: number
  titre: string
  isbn?: string
  resume?: string
  datePublication?: string
  auteur?: {
    id: number
    nom: string
    prenom: string
  }
  categorie?: {
    id: number
    nom: string
    couleur?: string
  }
  proprietaire: {
    id: number
    nom: string
    prenom: string
    email: string
  }
}

interface Auteur {
  id: number
  nom: string
  prenom: string
  biographie?: string
  dateNaissance?: string
  dateDeces?: string
}

interface Categorie {
  id: number
  nom: string
  description?: string
  couleur?: string
}

interface ApiResponse<T> {
  '@context': string
  '@id': string
  '@type': string
  totalItems?: number
  member?: T[]
}

export const useApi = () => {
  const config = useRuntimeConfig()
  
  // URL de base du backend (configurable via .env)
  const baseURL = 'http://localhost:8000'

  const apiCall = async <T>(endpoint: string, options: ApiOptions = {}): Promise<T> => {
    const { method = 'GET', headers = {}, body } = options

    try {
      const response = await $fetch<T>(`${baseURL}${endpoint}`, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/ld+json',
          ...headers
        },
        body: body ? JSON.stringify(body) : undefined
      })

      return response
    } catch (error) {
      console.error(`Erreur API pour ${endpoint}:`, error)
      throw error
    }
  }

  // Récupérer tous les livres
  const getBooks = async (): Promise<Livre[]> => {
    const response = await apiCall<ApiResponse<Livre>>('/api/livres')
    return response.member || []
  }

  // Récupérer un livre par ID
  const getBook = async (id: number): Promise<Livre> => {
    return await apiCall<Livre>(`/api/livres/${id}`)
  }

  // Récupérer tous les auteurs
  const getAuthors = async (): Promise<Auteur[]> => {
    const response = await apiCall<ApiResponse<Auteur>>('/api/auteurs')
    return response.member || []
  }

  // Récupérer toutes les catégories
  const getCategories = async (): Promise<Categorie[]> => {
    const response = await apiCall<ApiResponse<Categorie>>('/api/categories')
    return response.member || []
  }

  // Récupérer les statistiques pour la page d'accueil
  const getStats = async () => {
    try {
      const [books, authors, categories] = await Promise.all([
        getBooks(),
        getAuthors(),
        getCategories()
      ])

      return {
        livres: books.length.toLocaleString(),
        auteurs: authors.length.toLocaleString(),
        categories: categories.length.toLocaleString()
      }
    } catch (error) {
      // Valeurs par défaut en cas d'erreur
      return {
        livres: '0',
        auteurs: '0',
        categories: '0'
      }
    }
  }

  return {
    apiCall,
    getBooks,
    getBook,
    getAuthors,
    getCategories,
    getStats
  }
}
