<script setup lang="ts">
import { router } from '@/plugins/1.router';

const isMounted = ref(false);
onMounted(() => {
  isMounted.value = true;
});
// 👉 Store
const search = ref(router.currentRoute.value.query.search?? '')
const itemsPerPage = ref<string>(router.currentRoute.value.query.limit as string ?? 10)
const page = ref<any>(router.currentRoute.value.query.page?? 1)
const orderBy = ref(router.currentRoute.value.query.sort_direction?? 'asc')
const sortBy = ref(router.currentRoute.value.query.sort_by?? null)
const selectedRows = ref([])

// Update data table options 
const updateOptions = (options: any) => {
  if (!isMounted.value) return;

  if(options?.sortBy) {
    sortBy.value = options?.sortBy[0]?.key
    orderBy.value = options?.sortBy[0]?.order
  }

  router.replace({ name: 'patronage-participant', query: { 
    ...(search.value? { search: search.value } : null),
    limit: itemsPerPage.value,
    page: page.value,
    ...(sortBy.value? { sort_by: sortBy.value } : null),
    ...(orderBy.value? { sort_direction: orderBy.value } : null),
  } as any })
}

const query = {
  search: search,
  limit: itemsPerPage,
  page: page,
  sort_by: sortBy.value ? sortBy : 'id',
  sort_direction: orderBy
}
// 👉 Fetching users
const { data: participantData, execute } = await useApi<any>(createUrl('/v1/participants', { query }))

const participants = computed((): any[] => participantData.value?.data)
const total_participant = computed(() => participantData.value?.total)

const isDialogVisible = ref(false)

// 👉 Delete user
const pending_delete_participant = ref()
const deleteUser = async () => {
  await $api(`/v1/participants/${pending_delete_participant.value?.id}`, {
    method: 'DELETE',
  })

  // Delete from selectedRows
  const index = selectedRows.value.findIndex(row => row === pending_delete_participant.value?.id)
  if (index !== -1)
    selectedRows.value.splice(index, 1)

  execute()
  isDialogVisible.value = false
}


// Headers
const headers = [
  { title: '#', key: 'id' }, 
  { title: 'មុខងារ', key: 'title', sortable: false },
  { title: 'ឈ្មោះ', key: 'name' },
  { title: 'តួនាទី', key: 'position', sortable: false },
  { title: 'ចំនួនបេសកកម្ម', key: 'missions_count'},
  { title: 'បេសកកម្មចុងក្រោយ', key: 'lastest_mission', sortable: false },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]


</script>

<template>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4 items-center">
       
        <!-- 👉 Add user button -->
        <!-- <VBtn prepend-icon="tabler-plus" to="/patronage/participant/create"> បង្កើតថ្មី </VBtn> -->
        
        <VSpacer />

        <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
          <!-- 👉 Search  -->
          <div style="inline-size: 18rem;"> 
            <AppTextField v-model="search" placeholder="ស្វែងរក" 
              @vue:updated="updateOptions" dense outlined single-line
              append-inner-icon="tabler-search" />
          </div>
        </div>

      </VCardText>

      <VDivider />

      <!-- SECTION datatable -->
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:model-value="selectedRows"
        v-model:page="page"
        :items="participants"
        item-value="id"
        :items-length="total_participant"
        :headers="headers"
        class="text-secondary"
        show-select
        @update:options="updateOptions"
      >
        <!-- # -->
        <template #item.id="{ item }">
          <span class="text-primary">
            {{ item.id }}
          </span>
        </template>

        <template #item.title="{ item }">
          <span>
            {{ item.title?? 'គ្មានមុខងារ' }}
          </span>
        </template>

        <template #item.position="{ item }">
          <span>
            {{ item.title?? 'គ្មានតួនាទី' }}
          </span>
        </template>

        <template #item.lastest_mission="{ item }">
          <span>
            {{ resolveDate(item.lastest_mission)?? 'គ្មានបេសកកម្ម' }}
          </span>
        </template>

        <template #item.missions_count="{ item }">
          <span>
            {{ convertToKhmerNumerals(item.missions_count) }} ដង
          </span>
        </template>


        

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="isDialogVisible = !isDialogVisible; pending_delete_participant = item">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn :to="{ name: 'patronage-participant-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="+itemsPerPage"
            :total-items="total_participant"
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
            <span class="opacity-70">តើអ្នកចង់លុប​​បញ្ចី <span class="text-red-500 font-medium opacity-100">{{ pending_delete_participant?.id }}</span> មែន?</span>
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
