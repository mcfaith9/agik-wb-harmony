import axios from 'axios'

export const useRoles = () => {
  const getRoles = () => axios.get('/api/roles')
  const createRole = (data) => axios.post('/api/roles', data)
  const updateRole = (id, data) => axios.put(`/api/roles/${id}`, data)
  const deleteRole = (id) => axios.delete(`/api/roles/${id}`)
  const assignRole = (userId, roleId) =>
    axios.post(`/api/users/${userId}/assign-role`, { role_id: roleId })
  const unassignRole = (userId, roleId) =>
    axios.post(`/api/users/${userId}/unassign-role`, { role_id: roleId })
  const getUserRoles = (userId) =>
    axios.get(`/api/users/${userId}/roles`)

  return {
    getRoles,
    createRole,
    updateRole,
    deleteRole,
    assignRole,
    unassignRole,
    getUserRoles
  }
}
