// src/Composables/useTable.js
import { ref, computed, watch } from 'vue'

export default function useTable(data, options = {}) {
  const { sortColumn: initialSortColumn, perPage: initialPerPage } = options

  const currentPage = ref(1)
  const sortColumn = ref(initialSortColumn || null)
  const sortDirection = ref('asc') // 'asc' or 'desc'
  const perPage = ref(initialPerPage || 10)

  // Computed property to get the actual data to work with (reactive)
  // This expects `data` to be a ref or computed property itself, like `preFilteredInvoices`
  const processedData = computed(() => {
    let list = data.value // Access the value of the ref/computed

    // Apply sorting
    if (sortColumn.value) {
      list = [...list].sort((a, b) => {
        let aVal = a[sortColumn.value]
        let bVal = b[sortColumn.value]

        // Handle nested properties for client.name
        if (sortColumn.value === 'client.name') {
            aVal = a.client?.name || '';
            bVal = b.client?.name || '';
        }
        // Handle numeric sorting for 'total'
        if (sortColumn.value === 'total') {
            aVal = parseFloat(aVal) || 0;
            bVal = parseFloat(bVal) || 0;
        }

        if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1
        if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1
        return 0
      })
    }

    return list
  })

  // Computed property for total pages
  const totalPages = computed(() => {
    return Math.ceil(processedData.value.length / perPage.value)
  })

  // Computed property for paginated data
  const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * perPage.value
    const end = start + perPage.value
    return processedData.value.slice(start, end)
  })

  // Watch for changes in the processedData and reset currentPage if needed
  watch(processedData, () => {
    if (currentPage.value > totalPages.value && totalPages.value > 0) {
      currentPage.value = totalPages.value;
    } else if (totalPages.value === 0) {
        currentPage.value = 1; // Or 0, depending on your preference for empty states
    }
  });


  const sortBy = (column) => {
    if (sortColumn.value === column) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
      sortColumn.value = column
      sortDirection.value = 'asc' // Default to ascending when changing column
    }
  }

  return {
    currentPage,
    paginatedData,
    sortBy,
    sortColumn,
    sortDirection,
    totalPages,
    perPage, // Expose perPage if you want to allow changing it
  }
}