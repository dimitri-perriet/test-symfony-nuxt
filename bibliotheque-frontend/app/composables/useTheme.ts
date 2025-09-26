export const useTheme = () => {
  const theme = ref<'light' | 'dark'>('light')

  const setTheme = (newTheme: 'light' | 'dark') => {
    theme.value = newTheme
    
    if (process.client) {
      document.documentElement.setAttribute('data-theme', newTheme)
      localStorage.setItem('theme', newTheme)
    }
  }

  const toggleTheme = () => {
    const newTheme = theme.value === 'light' ? 'dark' : 'light'
    setTheme(newTheme)
  }

  const initTheme = () => {
    if (process.client) {
      // Récupérer le thème sauvegardé ou utiliser la préférence système
      const savedTheme = localStorage.getItem('theme') as 'light' | 'dark' | null
      
      if (savedTheme) {
        setTheme(savedTheme)
      } else {
        // Détecter la préférence système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        setTheme(prefersDark ? 'dark' : 'light')
      }
    }
  }

  return {
    theme: readonly(theme),
    setTheme,
    toggleTheme,
    initTheme
  }
}