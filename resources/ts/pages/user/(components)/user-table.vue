<script setup lang="ts">
import { router } from '@/plugins/1.router';
import { dateFormatter } from '@/utils/date';


const isMounted = ref(false);
onMounted(() => {
  isMounted.value = true;
});

const searchQuery = ref(router.currentRoute.value.query.search?? '')
const selectedStatus = ref(router.currentRoute.value.query.by_status?? null)
const itemsPerPage = ref<any>(router.currentRoute.value.query.limit as string ?? 10)
const page = ref<any>(router.currentRoute.value.query.page?? 1)
const sortBy = ref(router.currentRoute.value.query.sort_by?? null)
const orderBy = ref(router.currentRoute.value.query.sort_direction?? 'asc')

const selectedRows = ref([])

// Update data table options
const updateOptions = (options: any) => {
  if(!isMounted.value) return;
  if(options?.sortBy) {
    sortBy.value = options?.sortBy[0]?.key
    orderBy.value = options?.sortBy[0]?.order
  }

  router.replace({ name: 'user', query: { 
    ...(searchQuery.value? { search: searchQuery.value } : null),
    limit: itemsPerPage.value,
    page: page.value,
    ...(selectedStatus.value? { status: selectedStatus.value } : null),
    ...(orderBy.value? { sort_direction: orderBy.value } : null),
    ...(sortBy.value? { sort_by: sortBy.value } : null),
  } as any })
}

const query = {
  search: searchQuery,
  status: selectedStatus.value,
  limit: itemsPerPage,
  page,
  sort_by: sortBy.value ? sortBy : 'id',
  sort_direction: orderBy
}

// 👉 Fetching users
const { data: usersData, execute } = await useApi<any>(createUrl('/v1/users', { query }))

const users = computed((): any[] => usersData.value?.users.data)
const totalUsers = computed(() => usersData.value?.users.total)



const resolveUserStatusVariant = (stat: string) => {
  if (!stat)
    return 'warning'
  else  return 'success'
}

// reformatting date from ISO to custom string
const resolveDate = (date: string) => {
  if (!date)
    return 'មិនបានកំណត់'
  else {
    return dateFormatter.formatDate(date);
  }
}

const isDialogVisible = ref(false)

// 👉 Delete user
const pending_delete_user = ref()
const deleteUser = async () => {
  await $api(`/v1/users/${pending_delete_user.value?.id}`, {
    method: 'DELETE',
  })

  // Delete from selectedRows
  const index = selectedRows.value.findIndex(row => row === pending_delete_user.value?.id)
  if (index !== -1)
    selectedRows.value.splice(index, 1)

  // refetch User
  // TODO: Make this async
  execute()
  isDialogVisible.value = false
}

// Headers
const headers = [
  { title: '#', key: 'id' }, 
  { title: 'ឈ្មោះអ្នកប្រើប្រាស់', key: 'name' }, 
  { title: 'អុីម៉េល', key: 'email' },
  { title: 'តួនាទី', key: 'role' },
  { title: 'ចូលចុងក្រោយ', key: 'lastest_login' },
  { title: 'ស្ថានភាព', key: 'status' },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

const status = [
  { title: 'ដំណើរការ', value: true },
  { title: 'មិនដំណើរការ', value: false },
]

</script>

<template>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4 items-center">

        <!-- 👉 Add user button -->
        <VBtn prepend-icon="tabler-plus" to="/user/create"> បង្កើតថ្មី </VBtn>
        
        <VSpacer />

        <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
          <!-- 👉 Search  -->
          <div style="inline-size: 18rem;">
            <AppTextField v-model="searchQuery" placeholder="ស្វែងរក" @vue:updated="updateOptions"  outlined single-line
            append-inner-icon="tabler-search" clear-icon="tabler-x"/>
          </div>
        </div>

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

        <!-- # -->
        <template #item.id="{ item }">
          <div class="d-flex flex-column">
            <span class="text-primary">
              {{ item.id }}
            </span>
          </div>
        </template>


        <!-- User -->
        <template #item.name="{ item }">
          <div class="d-flex align-center gap-x-4">
            <VAvatar
              size="34"
              :variant="!item.avatar ? 'tonal' : undefined"
              color="primary"
            >
              <VImg
                v-if="item.avatar"
                :src="item.avatar"
              />
              <span v-else>{{ avatarText(item.name) }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <h6 class="text-base">
                <span
                  
                  class="text-link"
                >
                  {{ item.name }}
                </span>
                <!-- <RouterLink
                  :to="{ name: 'apps-user-view-id', params: { id: item.id } }"
                  class="font-weight-medium text-link"
                >
                  {{ item.name }}
                </RouterLink> -->
              </h6>
            </div>
          </div>
        </template>



        <!-- Plan -->
        <template #item.role="{ item }">
          <div class="text-body-1">
            {{ 'អ្នកគ្រប់គ្រង់ប្រព័ន្ធ' }}
          </div>
        </template>


        <!-- 👉 lastest login -->
        <template #item.lastest_login="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div class="text-body-1">
              {{ resolveDate(item.lastest_login) }}
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
          <IconBtn @click="isDialogVisible = !isDialogVisible; pending_delete_user = item">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn :to="{ name: 'user-update-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>

        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="+itemsPerPage"
            :total-items="totalUsers"
            @update:options="updateOptions"
          />
        </template>
      </VDataTableServer>
      <!-- SECTION -->
    </VCard>





    <VDialog v-model="isDialogVisible" max-width="600">
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />
      <!-- Dialog Content -->
      <VCard class="text-secondary">
        <div class="bg-gradient-to-b from-[#FF830F]/10 to-[#FF830F]/5">
          <VCardTitle>
            <span class="text-xl text-secondary">សូមបញ្ចាក់</span>
          </VCardTitle>
          <VCardText>
            <span class="opacity-70">តើអ្នកចង់លុបអ្នកប្រើប្រាស់ ឈ្មោះ​ <span class="text-red-500 font-medium opacity-100">{{ pending_delete_user?.name }}</span> មែន?</span>
          </VCardText>

          <VCardText class="d-flex justify-end flex-wrap gap-3">
            <VBtn variant="tonal" color="secondary" @click="isDialogVisible = false">
              បោះបង់ចោល
            </VBtn>
            <VBtn color="error" @click="deleteUser">
              លុបចេញ
            </VBtn>
          </VCardText>
        </div>
      </VCard>
    </VDialog>
</template>
