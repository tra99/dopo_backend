<script setup lang="ts">
import { router } from '@/plugins/1.router'
//TODO: check and fix warning problem in this page related to Vpagination
// 👉 Store
const search = ref(router.currentRoute.value.query.search?? '')
const with_mission = ref(router.currentRoute.value.query.with_mission?? null)
const itemsPerPage = ref<string>(router.currentRoute.value.query.limit as string ?? 10)
const page = ref<any>(router.currentRoute.value.query.page?? 1)
const orderBy = ref(router.currentRoute.value.query.sort_direction?? 'asc')
const selectedRows = ref([])

// Update data table options 
const updateOptions = (options: any) => {
  // sortBy.value = options.sortBy[0]?.key
  // orderBy.value = options.sortBy[0]?.orderBy

  router.replace({ name: 'patronage-school', query: { 
    ...(search.value? { search: search.value } : null),
    limit: itemsPerPage.value,
    page: page.value,
    ...(with_mission.value? { with_mission: with_mission.value } : null),
    ...(orderBy.value? { sort_direction: orderBy.value } : null),
  } as any })
}

const query = {
  search: search,
  limit: itemsPerPage,
  page: page,
  with_mission: with_mission,
  sort_direction: orderBy,
}
// 👉 Fetching users
const { data: missionData, execute } = await useApi<any>(createUrl('/v1/schools', { query }))

const mission = computed((): any[] => missionData.value?.data)
const totalmission = computed(() => missionData.value?.total)

const isDialogVisible = ref(false)

// 👉 Delete user
const pending_delete_mission = ref()
const deleteUser = async () => {
  await $api(`/v1/missions/${pending_delete_mission.value?.id}`, {
    method: 'DELETE',
  })

  // Delete from selectedRows
  const index = selectedRows.value.findIndex(row => row === pending_delete_mission.value?.id)
  if (index !== -1)
    selectedRows.value.splice(index, 1)

  // refetch User
  // TODO: Make this async
  execute()
  isDialogVisible.value = false
}

function resolveLatestDate(array: any[]){
  if(array.length == 0) return null;
  return array.reduce((latest, current) => new Date(current.created_at) > new Date(latest.created_at) ? current : latest)?.created_at || 'គ្មាន' 
}

// Headers
const headers = [
  { title: '#', key: 'id' }, 
  { title: 'វិទ្យាល័យ', key: 'school_name_kh', sortable: false },
  { title: 'ខេត្ត', key: 'province_kh', sortable: false },
  { title: 'ពិន្ទុសរុប', key: 'total_score', sortable: false },
  { title: 'ចំនួនបេសកកម្ម', key: 'mission_count', sortable: false },
  { title: 'បេសកកម្មចុងក្រោយ', key: 'latest_mission', sortable: false },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

const missionOptions = [
  { title: 'ទាំងអស់', value: '' }, 
  { title: 'មានបេសកកម្ម', value: 'true' }, 
  { title: 'មិនមានបេសកកម្ម', value: 'false' }, 
]

</script>

<template>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4 items-center">
        
        <VSpacer />

        <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
          <!-- 👉 Search  -->
          <div style="inline-size: 18rem;"> 
            <AppTextField v-model="search" placeholder="ស្វែងរក" 
              @vue:updated="updateOptions" dense outlined single-line
              append-inner-icon="tabler-search" />
          </div>
        </div>

        <VCol cols="4" sm="2">
          <AppSelect v-model="with_mission" placeholder="ស្ថានភាពបេសកកម្ម" :items="missionOptions"
            clearable @vue:updated="updateOptions"
            clear-icon="tabler-x"/>
        </VCol>

      </VCardText>

      <VDivider />

      <!-- SECTION datatable -->
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:model-value="selectedRows"
        v-model:page="page"
        :items="mission"
        item-value="id"
        :items-length="totalmission"
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

        <!-- misison count -->
        <template #item.mission_count="{ item }">
            {{ item.missions.length }}
        </template>

        <!-- total score -->
        <template #item.total_score="{ item }">
            {{ item?.score || 0 }}
        </template>

        <!-- latest mission -->
        <template #item.latest_mission="{ item }">
            {{ item?.missions && resolveDate(resolveLatestDate(item?.missions)) }}
        </template>


        <!-- Actions -->
        <template #item.actions="{ item }">
          <!-- <IconBtn @click="isDialogVisible = !isDialogVisible; pending_delete_mission = item">
            <VIcon icon="tabler-trash" />
          </IconBtn> -->

          <IconBtn :to="{ name: 'patronage-school-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="+itemsPerPage"
            :total-items="totalmission"
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
            <span class="opacity-70">តើអ្នកចង់លុប​​បញ្ចី <span class="text-red-500 font-medium opacity-100">{{ pending_delete_mission?.id }}</span> មែន?</span>
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
