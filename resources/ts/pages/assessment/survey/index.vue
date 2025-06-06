<script setup lang="ts">
import { router } from '@/plugins/1.router'
import { resolveDate, resolveStatusColor } from '@/utils/resolver'

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

  router.replace({ name: 'assessment-survey', query: { 
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
const { data: surveyData, execute } = await useApi<any>(createUrl('/v1/surveys', { query }))

const survey = computed((): any[] => surveyData.value?.data)
const totalsurvey = computed(() => surveyData.value?.total)

const isDialogVisible = ref(false)

// 👉 Delete user
const pending_delete_survey = ref()
const deleteUser = async () => {
  await $api(`/v1/surveys/${pending_delete_survey.value?.id}`, {
    method: 'DELETE',
  })

  // Delete from selectedRows
  const index = selectedRows.value.findIndex(row => row === pending_delete_survey.value?.id)
  if (index !== -1)
    selectedRows.value.splice(index, 1)

  // refetch User
  // TODO: Make this async
  execute()
  isDialogVisible.value = false
}



// Headers
const headers = [
  { title: '#', key: 'id', sortable: false  }, 
  { title: 'ឈ្មោះ', key: 'title', sortable: false  }, 
  { title: 'ចំនួនសំណួរ', key: 'questions_count', sortable: false },
  { title: 'បានឆ្លើយ', key: 'answers_count', sortable: false },
  { title: 'កាលបរិចេ្ឆទ', key: 'date', sortable: false  },
  { title: 'ស្ថានភាព', key: 'is_published', sortable: false },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

const status = [
  { title: 'ដំណើរការ', value: 'true' },
  { title: 'មិនដំណើរការ', value: 'false' },
]

</script>

<template>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4 items-center">
       
        <!-- 👉 Add user button -->
        <VBtn prepend-icon="tabler-plus" to="/assessment/survey/create"> បង្កើតថ្មី </VBtn>
        
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
        :items="survey"
        item-value="id"
        :items-length="totalsurvey"
        :headers="headers"
        class="text-secondary"
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


        <template #item.title="{ item }">
          <span>
            {{ item.title }}
          </span>
        </template>


        <!-- 👉 date -->
        <template #item.date="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div class="text-body-1 text-secondary">
              {{ resolveDate([item.start_date, item.end_date]) }}
            </div>
          </div>
        </template>

        <!-- Status -->
        <template #item.is_published="{ item }">
          <VChip
            :color="resolveStatusColor(item.is_published)"
            size="small"
            label
            class="text-capitalize"
          >
          {{ item.is_published ? 'ដំណើរការ':'មិនដំណើរការ' }}

          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="isDialogVisible = !isDialogVisible; pending_delete_survey = item">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn :to="{ name: 'assessment-survey-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="+itemsPerPage"
            :total-items="totalsurvey"
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
            <span class="opacity-70">តើអ្នកចង់លុប​​បញ្ចី <span class="text-red-500 font-medium opacity-100">{{ pending_delete_survey?.title }}</span> មែន?</span>
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
