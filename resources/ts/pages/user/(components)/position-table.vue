<script setup lang="ts">


// 👉 Store
const searchQuery = ref('')
const selectedRole = ref()
const selectedStatus = ref()

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const selectedRows = ref([])

// Update data table options
const updateOptions = (options: any) => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
}

// Headers
const headers = [
  { title: '#', key: 'id' }, 
  { title: 'មុខងារ', key: 'name' }, 
  { title: 'ចំនួនអ្នកប្រើប្រាស់', key: 'users_count', sortable: false},
  { title: 'ស្ថានភាព', key: 'status' },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

// async function fetchUsers(){
//   const res = await useApi<any>('/v1/roles')
//   return res.data.value;
// }
// 👉 Fetching users
const { data: usersData, execute } = await useApi<any>(createUrl('/v1/roles', {
  query: {
    search: searchQuery, //work
    status: selectedStatus, //work
    limit: itemsPerPage,
    page,
    sort_by: sortBy,
    sort_direction:orderBy,
  },
}))

// const usersData = ref<any>(await fetchUsers());

const users = computed((): any[] => usersData.value?.data)
const totalUsers = computed(() => usersData.value?.data.length)

// 👉 search filters
const roles = [
  { title: 'អ្នកគ្រប់គ្រងប្រព័ន្ធ', value: 'អ្នកគ្រប់គ្រងប្រព័ន្ធ' },
  { title: 'អ្នកប្រើប្រាស់', value: 'អ្នកប្រើប្រាស់' },
  { title: 'អភិបាលប្រព័ន្ធ', value: 'អភិបាលប្រព័ន្ធ' },
]

// const plans = [
//   { title: 'Basic', value: 'basic' },
//   { title: 'Company', value: 'company' },
//   { title: 'Enterprise', value: 'enterprise' },
//   { title: 'Team', value: 'team' },
// ]

const status = [
  { title: 'ដំណើរការ', value: 'true' },
  { title: 'មិនដំណើរការ', value: 'false' },
]

const resolveUserStatusVariant = (stat: string) => {
  if (!stat)
    return 'warning'
  else  return 'success'

  // return 'primary'
}


// 👉 Delete user
const deleteUser = async (id: number) => {
  await $api(`/apps/users/${id}`, {
    method: 'DELETE',
  })

  // Delete from selectedRows
  const index = selectedRows.value.findIndex(row => row === id)
  if (index !== -1)
    selectedRows.value.splice(index, 1)

  // refetch User
  // TODO: Make this async
  execute()
}
</script>

<template>
  <section>

    <VCard>
      <VCardText class="d-flex flex-wrap gap-4 items-center">
        <!-- 👉 Select Role -->
        <VCol
            cols="12"
            sm="2"
          >
            <AppSelect
              v-model="selectedRole"
              placeholder="សកម្មភាព"
              :items="roles"
              clearable
              clear-icon="tabler-x"
            />
          </VCol>

        <!-- 👉 Add user button -->
        <VBtn prepend-icon="tabler-plus" @click="console.log('add new user')"> បង្កើតថ្មី </VBtn>
        
        <VSpacer />

        <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
          <!-- 👉 Search  -->
          <div style="inline-size: 18rem;">
            <AppTextField v-model="searchQuery" placeholder="ស្វែងរក" 
              @vue:updated="updateOptions" dense outlined single-line
              append-inner-icon="tabler-search" clear-icon="tabler-x"/>
          </div>

          <!-- 👉 Export button (commented for now, might need this later)-->
          <!-- <VBtn variant="tonal" color="secondary" prepend-icon="tabler-upload">
            Export
          </VBtn> -->
        </div>

        <!-- 👉 Select Status -->
        <VCol cols="4" sm="2">
            <AppSelect v-model="selectedStatus" placeholder="ស្ថានភាព" :items="status"
              clearable
              clear-icon="tabler-x"/>
          </VCol>
      </VCardText>

      <VDivider />

      <!-- SECTION datatable -->
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:model-value="selectedRows"
        v-model:page="page"
        :items="users"
        item-value="id"
        :items-length="totalUsers"
        :headers="headers"
        class="text-no-wrap"
        show-select
        @update:options="updateOptions"
      >
        <!-- position -->
        <template #item.name="{ item }">
          <div class="text-body-1 text-high-emphasis text-capitalize">
            {{ item.name }}
          </div>
        </template>


        <!-- 👉 Role -->
        <template #item.users_count="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div class="text-capitalize text-body-1">
              {{ item.users_count }} នាក់
            </div>
          </div>
        </template>

        <!-- Status -->
        <template #item.status="{ item }">
          <VChip
            :color="resolveUserStatusVariant(item.status)"
            size="small"
            label
            class="text-capitalize"
          >
            {{ item.status ? 'ដំណើរការ':'មិនដំណើរការ' }}
          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="deleteUser(item.id)">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn>
            <VIcon icon="tabler-eye" />
          </IconBtn>

          <VBtn
            icon
            variant="text"
            color="medium-emphasis"
          >
            <VIcon icon="tabler-dots-vertical" />
            <VMenu activator="parent">
              <VList>
                <VListItem :to="{ name: 'apps-user-view-id', params: { id: item.id } }">
                  <template #prepend>
                    <VIcon icon="tabler-eye" />
                  </template>

                  <VListItemTitle>View</VListItemTitle>
                </VListItem>

                <VListItem link>
                  <template #prepend>
                    <VIcon icon="tabler-pencil" />
                  </template>
                  <VListItemTitle>Edit</VListItemTitle>
                </VListItem>

                <VListItem @click="deleteUser(item.id)">
                  <template #prepend>
                    <VIcon icon="tabler-trash" />
                  </template>
                  <VListItemTitle>Delete</VListItemTitle>
                </VListItem>
              </VList>
            </VMenu>
          </VBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="totalUsers"
          />
        </template>
      </VDataTableServer>
      <!-- SECTION -->
    </VCard>
  </section>
</template>
