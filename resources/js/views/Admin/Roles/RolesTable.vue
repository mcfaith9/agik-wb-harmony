<script setup lang="ts">
  import { h, ref } from "vue"
  import { useRoles } from '@/composables/useRoles'
  import McTable from "@/components/common/Table/McTable.vue"
  import McTableAction from "@/components/common/Table/McTableAction.vue"
  import RoleForm from "@/views/Admin/Roles/RoleForm.vue"
  import { Ellipsis } from "lucide-vue-next"

  const { deleteRole } = useRoles()
  const tableRef = ref(null)
  const refreshKey = ref(0)
  const dataSource = ref([])
  const isFormOpen = ref<boolean>(false)
  const editRole = ref(null)
  const mode = ref<'create' | 'edit'>('create')

  // Format attribute keys like "can_edit_users" => "Can Edit Users"
  function formatAttributes(attrs) {
    if (!attrs || typeof attrs !== 'object') return []
    return Object.entries(attrs)
      .filter(([_, value]) => value)
      .map(([key]) => {
        const label = key.replace(/^can_/, '')
                         .replace(/_/g, ' ')
                         .replace(/\b\w/g, l => l.toUpperCase())
        return `Can ${label}`
      })
  }

  const handleEdit = (role) => {
    editRole.value = role
    mode.value = 'edit'
    isFormOpen.value = true
  }

  const handleCreated = (role) => {
    console.log("✅ Role created:", role)
    patchRow(role) // Add instead of re-fetch
  }

  const handleDelete = async (role) => {
    if (!role?.id) return

    try {
      const confirmed = confirm(`Are you sure you want to delete "${role.label}"?`)
      if (!confirmed) return

      await deleteRole(role.id)

      const index = dataSource.value.findIndex(r => r.id === role.id)
      if (index !== -1) {
        dataSource.value.splice(index, 1)
        tableRef.value?.setData?.([...dataSource.value])
      }
    } catch (error) {
      alert("Failed to delete role.")
      console.error("Delete error:", error)
    }
  }

  const patchRow = (newOrUpdatedRole) => {
    console.log("🔁 patchRow received:", newOrUpdatedRole)

    const index = dataSource.value.findIndex(r => r.id === newOrUpdatedRole.id)

    if (index !== -1) {
      dataSource.value[index] = { ...dataSource.value[index], ...newOrUpdatedRole }
    } else {
      dataSource.value.unshift(newOrUpdatedRole)

      if (dataSource.value.length > 10) {
        dataSource.value.pop()
      }
    }

    tableRef.value?.setData?.([...dataSource.value])
  }

  const columns = [
    { accessorKey: 'label', header: 'Label', meta: { class: 'w-1/4' } },
    { accessorKey: 'name', header: 'Name', meta: { class: 'w-1/4' } },
    {
      id: 'attributes',
      header: 'Attributes',
      cell: info => {
        const tags = formatAttributes(info.row.original.attributes)
        return h(
          'div',
          tags.map(tag =>
            h('span', {
              class:
                'inline-block bg-blue-500 text-xs font-semibold mr-1 mb-1 px-2.5 py-0.5 dark:text-gray-200'
            }, tag)
          )
        )
      },
      meta: { class: 'w-2/4' }
    },
    {
      id: 'actions',
      header: '',
      cell: info =>
        h(McTableAction, {
          row: info.row.original,
          onEdit: () => handleEdit(info.row.original),
          onDelete: () => handleDelete(info.row.original)
        }),
      meta: { class: 'text-right w-[40px]' }
    }
  ]

  const transformData = (response) => {
    dataSource.value = response.data

    return {
      data: response.data,
      from: response.from,
      to: response.to,
      total: response.total,
      last_page: response.last_page,
      current_page: response.current_page,
    }
  }

  defineExpose({
    handleCreated,
  })
</script>

<template>
  <McTable
    ref="tableRef"
    :columns="columns"
    :data="dataSource"
    :transformData="transformData"
    endpoint="/api/roles"
    :perPage="10"
  />

  <RoleForm
    :isOpen="isFormOpen"
    :mode="mode"
    :roleToEdit="editRole"
    @close="isFormOpen = false"
    @updated="patchRow"
  />
</template>
