// src/services/orderService.js

import {
    request
} from '../utils/request'

const BASE_PATH = 'orders'

export const OrderService = {
    getList: async (filter = {}) => {
        const {
            txt_search
        } = filter

        const params = new URLSearchParams()

        if (txt_search) {
            params.append('txt_search', txt_search)
        }

        return request(`${BASE_PATH}?${params.toString()}`, 'get')
    },

    create: async data => {
        return request(BASE_PATH, 'post', data)
    },

    getOne: async id => {
        return request(`${BASE_PATH}/${id}`, 'get')
    },

    delete: async id => {
        return request(`${BASE_PATH}/${id}`, 'delete')
    }
}