import axios from 'axios';
import { toast } from '../store/toast';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

apiClient.interceptors.response.use(
    response => response,
    error => {
        const message = error.response?.data?.message || 'An unknown API error occurred.';
        toast.show(message, 'error');
        return Promise.reject(error);
    }
);

export default apiClient;
