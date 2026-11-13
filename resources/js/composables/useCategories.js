import { ref } from 'vue'

export const useCategories = () => {
  const categories = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch('/api/storefront/categories')
      if (!response.ok) throw new Error('Failed to fetch categories')

      const { data } = await response.json()
      categories.value = data
      return data
    } catch (e) {
      error.value = e.message
      categories.value = []
    } finally {
      loading.value = false
    }
  }

  const fetchOne = async (uuid) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`/api/storefront/categories/${uuid}`)
      if (!response.ok) throw new Error('Failed to fetch category')

      const { data } = await response.json()
      return data
    } catch (e) {
      error.value = e.message
      return null
    } finally {
      loading.value = false
    }
  }

  return {
    categories,
    loading,
    error,
    fetchAll,
    fetchOne,
  }
}
