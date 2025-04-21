<script setup lang="ts">
import { router } from '@/plugins/1.router'
import { resolveDate } from '@/utils/resolver'

// 👉 Store
const search = ref(router.currentRoute.value.query.search?? '')
const selectedStatus = ref(router.currentRoute.value.query.by_status?? null)
const itemsPerPage = ref<string>(router.currentRoute.value.query.limit as string ?? 10)
const page = ref<any>(router.currentRoute.value.query.page?? 1)
const orderBy = ref(router.currentRoute.value.query.sort_direction?? 'asc')
const selectedRows = ref([])

// Update data table options 
const updateOptions = (options: any) => {
  // sortBy.value = options.sortBy[0]?.key
  // orderBy.value = options.sortBy[0]?.orderBy

  router.replace({ name: 'patronage-mission', query: { 
    ...(search.value? { search: search.value } : null),
    limit: itemsPerPage.value,
    page: page.value,
    ...(selectedStatus.value? { by_status: selectedStatus.value } : null),
    ...(orderBy.value? { sort_direction: orderBy.value } : null),
  } as any })
}

const query = {
  search: search,
  limit: itemsPerPage,
  page: page,
  by_status: selectedStatus,
  sort_direction: orderBy,
}
// 👉 Fetching users
const { data: missionData, execute } = await useApi<any>(createUrl('/v1/missions', { query }))

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

const resolveStatusVariant = (stat: string) => {
  if (stat == 'pending')
    return 'warning'
  else  return 'success'
}


// Headers
const headers = [
  { title: '#', key: 'id' }, 
  { title: 'សាលារៀន', key: 'school_name', sortable: false },
  { title: 'ទីតាំង', key: 'province', sortable: false },
  { title: 'កាលបរិច្ឆេទ', key: 'date' },
  { title: 'ចំនួនក្រុមការងារ', key: 'participants', sortable: false },
  { title: 'ស្ថានភាព', key: 'status', sortable: false },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

const status = [
  { title: 'បានបញ្ចប់ជាស្ថាពរ', value: 'null' },
  { title: 'មិនមានរបាយការណ៍', value: 'pending' },
] 

</script>

<template>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4 items-center">
       
        <!-- 👉 Add user button -->
        <VBtn prepend-icon="tabler-plus" to="/patronage/mission/create"> បង្កើតថ្មី </VBtn>
        
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
          <AppSelect v-model="selectedStatus" placeholder="ស្ថានភាព" :items="status"
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


        <template #item.school_name="{ item }">
          <div v-if="item.schools.length < 3" class="flex flex-col gap-1 justify-center text-nowrap">
            <span v-for="school in item?.schools">{{ school.school_name_kh }}</span>
          </div>
          <div v-if="item.schools.length > 2" class="flex gap-2 items-center">
            <div class="flex flex-col gap-1 justify-center text-nowrap">
              <span>{{ item.schools[0].school_name_kh }}</span>
              <span>{{ item.schools[1].school_name_kh }}</span>
            </div>
            <VBadge :offset-x="-18" class="v-badge--tonal cursor-pointer" :content="`+${item.schools.length -2}`">
              <VTooltip class="opacity-90" location="top" activator="parent" open-on-focus>
                <div class="flex flex-col">
                  <span v-for="school in item?.schools.slice(2)">{{ school.school_name_kh }},</span>
                </div> 
              </VTooltip>
            </VBadge>
          </div>
        </template>


        <template #item.province="{ item }">
          <div v-if="item.schools.length < 3" class="flex flex-col gap-1 justify-center text-nowrap">
            <span v-for="school in [...new Set(item?.schools.map(s => s.province_kh))]">
              {{ school }}
            </span>
          </div>
          <div v-if="item.schools.length > 2" class="flex gap-2 items-center">
            <div class="flex flex-col gap-1 justify-center text-nowrap">
              <span>{{ [...new Set(item.schools.map(s => s.province_kh))][0] }}</span>
              <span v-if="[...new Set(item.schools.map(s => s.province_kh))][1] !== [...new Set(item.schools.map(s => s.province_kh))][0]">
                {{ [...new Set(item.schools.map(s => s.province_kh))][1] }}
              </span>
            </div>
            <VBadge v-if="[...new Set(item.schools.map(s => s.province_kh))].length > 2"
              :offset-x="-18" 
              class="v-badge--tonal cursor-pointer" 
              :content="`+${Math.max([...new Set(item.schools.map(s => s.province_kh))].length - 2, 0)}`"
            >
              <VTooltip class="opacity-90" location="top" activator="parent" open-on-focus>
                <div class="flex flex-col">
                  <span v-for="school in [...new Set(item?.schools.map(s => s.province_kh))].slice(2)">
                    {{ school }},
                  </span>
                </div>
              </VTooltip>
            </VBadge>
          </div>
        </template>



        <!-- 👉 date -->
        <template #item.date="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div>
              {{ resolveDate([item.start_date, item.end_date]) }}
            </div>
          </div>
        </template>


        <!-- 👉 date -->
        <template #item.participants="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div>
              {{ item.participants.length || 0 }} នាក់
            </div>
          </div>
        </template>

        <!-- Status -->
        <template #item.status="{ item }">
          <VChip
            :color="resolveStatusVariant(item.status)"
            size="small"
            label
            class="text-capitalize"
          >
          {{ item.status ? status?.find(s => s.value === item.status)?.title : '' }}

          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="isDialogVisible = !isDialogVisible; pending_delete_mission = item">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn :to="{ name: 'patronage-mission-id', params: { id: item.id } }">
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
