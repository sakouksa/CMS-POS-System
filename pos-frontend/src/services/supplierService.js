import {
    request
} from '../utils/request' // Adjust this import based on your actual request utility helper path

const BASE_PATH = 'supplier'

export const supplierService = {
    /**
     * Fetch suppliers list based on search and status filters
     * @param {Object} params - { txt_search, is_active }
     */
    getList(params = {}) {
        return request(`${BASE_PATH}`, 'get', params)
    },

    /**
     * Create a new supplier profile row
     */
    create(data) {
        return request(`${BASE_PATH}`, 'post', data)
    },

    /**
     * Update an existing supplier profile matching runtime ID
     */
    update(id, data) {
        return request(`${BASE_PATH}/${id}`, 'put', data)
    },

    /**
     * Delete a supplier from the database ledger
     */
    delete(id) {
        return request(`${BASE_PATH}/${id}`, 'delete')
    }
}