import axios from 'axios';

export const fetchTokens = () => axios.get('/api/tokens');
export const createToken = (data) => axios.post('/api/tokens', data);
export const revokeToken = (id) => axios.delete(`/api/tokens/${id}`);
export const regenerateToken = (id) => axios.patch(`/api/tokens/${id}/regenerate`);