import axios from 'axios'

export const useRoles = () => {
  const getRoles = () => axios.get('/api/roles')
  const createRole = (data) => axios.post('/api/roles', data)
  const updateRole = (id, data) => axios.put(`/api/roles/${id}`, data)
  const assignRole = (userId, roleId) => axios.post(`/api/users/${userId}/assign-role`, { role_id: roleId })
  const getUserRoles = (userId) => axios.get(`/api/users/${userId}/roles`)

  return { getRoles, createRole, updateRole, assignRole, getUserRoles }
}
