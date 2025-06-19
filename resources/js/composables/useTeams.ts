import axios from 'axios'

export const useTeams = () => {
  const getTeams = () => axios.get('/api/teams')
  const createTeam = (data) => axios.post('/api/teams', data)
  const updateTeam = (id, data) => axios.put(`/api/teams/${id}`, data)
  const addUserToTeam = (teamId: number, userId: number) => {
    return axios.post(`/api/teams/${teamId}/add-user`, {
      user_id: userId,
    })
  }

  const removeUserFromTeam = (teamId: number, userId: number) => {
    return axios.post(`/api/teams/${teamId}/remove-user`, { user_id: userId })
  }

  return {
    getTeams,
    createTeam,
    updateTeam,
    addUserToTeam,
    removeUserFromTeam,
  }
}
