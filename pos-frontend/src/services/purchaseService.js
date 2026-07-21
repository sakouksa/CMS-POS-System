import {
  request
} from '../utils/request'

const BASE_PATH = 'purchases'

export const PurchaseService = {
  getList: async (filter = {}) => {
    const {
      txt_search,
      supplier_id,
      payment_status,
      page,
      limit
    } = filter
    const params = new URLSearchParams()

    if (txt_search) params.append('txt_search', txt_search)
    if (supplier_id) params.append('supplier_id', supplier_id)
    if (payment_status) params.append('payment_status', payment_status)

    if (page) params.append('page', page)
    if (limit) params.append('limit', limit)

    const url = `${BASE_PATH}?${params.toString()}`
    return request(url, 'get')
  },

  getOne: async id => {
    return request(`${BASE_PATH}/${id}`, 'get')
  },

  create: async data => {
    return request(BASE_PATH, 'post', data)
  },

  update: async (id, data) => {
    return request(`${BASE_PATH}/${id}`, 'put', data)
  },

  delete: async id => {
    return request(`${BASE_PATH}/${id}`, 'delete')
  }
}