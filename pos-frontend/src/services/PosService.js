import {
    request
} from '../utils/request'

const BASE_PATH = 'orders'

export const PosService = {
    getList: async () => {
        return request('products', 'get')
    },
    create: async (data) => {
        return request(BASE_PATH, 'post', data)
    }
}