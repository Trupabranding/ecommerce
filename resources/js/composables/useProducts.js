import { ref } from 'vue'

export const useProducts = () => {
  const products = ref([])
  const currentProduct = ref(null)
  const relatedProducts = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
  })

  const fetchAll = async (page = 1, perPage = 15, filters = {}) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams({
        page,
        per_page: perPage,
        ...filters,
      })

      const response = await fetch(`/api/storefront/products?${params}`)
      if (!response.ok) throw new Error('Failed to fetch products')

      const { data, meta } = await response.json()
      products.value = data
      pagination.value = meta
      return meta
    } catch (e) {
      error.value = e.message
      products.value = []
    } finally {
      loading.value = false
    }
  }

  const fetchOne = async (uuid) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`/api/storefront/products/${uuid}`)
      if (!response.ok) throw new Error('Failed to fetch product')

      const json = await response.json()
      currentProduct.value = json.data
      relatedProducts.value = json.related || []
      return json.data
    } catch (e) {
      error.value = e.message
      currentProduct.value = null
      relatedProducts.value = []
    } finally {
      loading.value = false
    }
  }

  const fetchByCategory = async (category, page = 1, perPage = 15) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams({ page, per_page: perPage })
      const response = await fetch(
        `/api/storefront/category/${category}?${params}`
      )
      if (!response.ok) throw new Error('Failed to fetch products')

      const { data, meta } = await response.json()
      products.value = data
      pagination.value = meta
      return meta
    } catch (e) {
      error.value = e.message
      products.value = []
    } finally {
      loading.value = false
    }
  }

  const search = async (query, page = 1, perPage = 15) => {
    return fetchAll(page, perPage, { search: query })
  }

  const sort = async (sortBy, page = 1, perPage = 15) => {
    return fetchAll(page, perPage, { sort: sortBy })
  }

  return {
    products,
    currentProduct,
    relatedProducts,
    loading,
    error,
    pagination,
    fetchAll,
    fetchOne,
    fetchByCategory,
    search,
    sort,
  }
}
