import { ref } from 'vue'

export const useOrders = () => {
  const order = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const orderNumber = ref(null)

  const create = async (orderData) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch('/api/storefront/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(orderData),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to create order')
      }

      const json = await response.json()
      order.value = json.data
      orderNumber.value = json.order_number
      return json.data
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const fetch = async (orderNum) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`/api/storefront/orders/${orderNum}`)
      if (!response.ok) throw new Error('Failed to fetch order')

      const json = await response.json()
      order.value = json.data
      return json.data
    } catch (e) {
      error.value = e.message
      order.value = null
    } finally {
      loading.value = false
    }
  }

  return {
    order,
    orderNumber,
    loading,
    error,
    create,
    fetch,
  }
}
