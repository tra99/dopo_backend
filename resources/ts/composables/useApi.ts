import { createFetch } from '@vueuse/core';
import { destr } from 'destr';

export const useApi = createFetch({
  baseUrl: import.meta.env.VITE_API_BASE_URL || '/api',
  fetchOptions: {
    headers: {
      Accept: 'application/json',
    },
  },
  options: {
    refetch: true,
    async beforeFetch({ options }) {
      const accessToken = useCookie('accessToken').value

      if (accessToken) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${accessToken}`,
        }
      }

      return { options }
    },
    afterFetch(ctx) {
      const { data, response } = ctx
      let parsedData = null
      
      try {
        parsedData = destr(data)
      } catch (error) {
        console.error('Error parsing response data:', error)
      }

      return { data: parsedData, response }
    },

    onFetchError(ctx) {
      // You can add toast here or retry logic// ✅ Handle 401
      if (ctx.response?.status === 401) {

        const accessToken = useCookie('accessToken')
        accessToken.value = null

        // Optional: redirect to login page
        window.location.href = '/login'
      }

      return ctx
    },
  },
})
