<script setup lang="ts">
  import { ref, onMounted, defineComponent, h } from 'vue'
  import axios from 'axios'
  import RolesTableDropdown from '@/views/Admin/Roles/RolesTableDropdown.vue'
  import { useRoles } from '@/composables/useRoles'
  import McTable from '@/components/common/Table/McTable.vue'

  const allRoles = ref([])
  const { getRoles, assignRole, unassignRole } = useRoles()

  onMounted(async () => {
    try {
      const res = await getRoles()
      allRoles.value = res.data
    } catch (err) {
      console.error('Failed to fetch roles:', err)
    }
  })

  const columns = [
    { accessorKey: 'first_name', header: 'First Name' },
    { accessorKey: 'last_name', header: 'Last Name' },
    { 
      accessorKey: 'email', 
      header: 'Email',
      cell: info =>
        h('div', { class: 'text-xs' }, info.getValue())
    },
    { 
    	accessorKey: 'phone', 
    	header: 'Phone', 
    	cell: info =>
    	  h('div', { class: 'text-xs' }, info.getValue() || '-')
    },
    {
      accessorKey: 'role',
      header: 'Roles',
      cell: info => {
        const user = info.row.original

        return h(
          defineComponent({
            setup() {
              return () => {
                const roles = user.roles || []
                return h('div', roles.length ? roles.map(r => r.name).join(', ') : '-')
              }
            }
          })
        )
      }
    },
    {
      id: 'actions',
      header: '',
      cell: info => {
        const user = info.row.original

        return h(
          defineComponent({
            setup() {
              return () => h(RolesTableDropdown, {
                roles: allRoles.value.data || [],
                assignedRoles: user.roles || [],
                userId: user.id,
                onAssignRole: async (roleId: number) => {
                  try {
                    const exists = user.roles?.some(r => r.id === roleId)

                    if (exists) {
                      await unassignRole(user.id, roleId)
                      user.roles = user.roles.filter(r => r.id !== roleId)
                    } else {
                      await assignRole(user.id, roleId)
                      const role = allRoles.value.data.find(r => r.id === roleId)
                      if (role) {
                        user.roles = [...user.roles, role]
                      }
                    }
                  } catch (err) {
                    console.error('Failed to toggle role:', err)
                  }
                }
              })
            }
          })
        )
      }
    }
  ]

  const transformData = response => ({
    data: response.data,
    from: response.from,
    to: response.to,
    total: response.total,
    last_page: response.last_page,
    current_page: response.current_page,
  })
</script>

<template>
  <McTable
    endpoint="/api/admin/users/list"
    :columns="columns"
    :perPage="10"
    :transformData="transformData"
  />
</template>
