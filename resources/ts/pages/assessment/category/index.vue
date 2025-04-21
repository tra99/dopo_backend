<script setup lang="ts">
import { router } from '@/plugins/1.router';
import { resolveDate } from '@/utils/resolver';

const isMounted = ref(false);
onMounted(() => {
  isMounted.value = true;
});
// 👉 Store
const search = ref(router.currentRoute.value.query.search?? '')
const itemsPerPage = ref<any>(router.currentRoute.value.query.limit as string ?? 10)
const page = ref<any>(router.currentRoute.value.query.page?? 1)
const selectedType = ref(router.currentRoute.value.query.type?? null)
const orderBy = ref(router.currentRoute.value.query.sort_direction?? 'asc')
const selectedRows = ref([])

// Update data table options 
const updateOptions = (options: any) => {
  if(!isMounted.value) return;

  router.replace({ name: 'assessment-category', query: { 
    ...(search.value? { search: search.value } : null),
    ...(selectedType.value? { type: selectedType.value } : null),
    limit: itemsPerPage.value,
    page: page.value,
    ...(orderBy.value? { sort_direction: orderBy.value } : null),
  } as any })
}

const query = {
  search,
  limit: itemsPerPage,
  page,
  sort_direction: orderBy,
  type: selectedType,
}

// 👉 Fetching users
const { data: categoriesData, execute } = await useApi<any>(createUrl('/v1/category-groups', { query }))

const categories = computed((): any[] => categoriesData.value?.data)
const totalcategories = computed(() => categoriesData.value?.total)

const isDialogVisible = ref(false)

// 👉 Delete user
const pending_delete_categories = ref()
const deleteUser = async () => {
  await $api(`/v1/categories/${pending_delete_categories.value?.id}`, {
    method: 'DELETE',
  })

  // Delete from selectedRows
  const index = selectedRows.value.findIndex(row => row === pending_delete_categories.value?.id)
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
  { title: 'ឈ្មោះ', key: 'title',sortable: false }, 
  { title: 'ចំនួនសំណួរ', key: 'questions_count', sortable: false },
  { title: 'កែប្រែចុងក្រោយ', key: 'updated_at', sortable: false },
  { title: 'សំរាប់ប្រភេទសាលារៀន', key: 'school_type', sortable: false },
  // { title: 'ដាក់ដំណើរការ', key: 'status', sortable: false },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

const types = [
  { title: 'សូចនករ', value: 'sochanakor'},
  { title: 'ស្តង់តា', value: 'standard' },
  { title: 'ក្រុម', value: 'group' }
  
]


</script>

<template>
    <VCard>
      <VCardText class="flex flex-wrap gap-4 items-center">
       
        <!-- 👉 Add user button -->
        <!-- <VBtn prepend-icon="tabler-plus" to="evaluation/create"> បង្កើតថ្មី </VBtn> -->
        
        <VSpacer />

        <!-- 👉 Search  -->
        <!-- <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
          <div style="inline-size: 18rem;"> 
            <AppTextField v-model="search" placeholder="ស្វែងរក" 
              @vue:updated="updateOptions" dense outlined single-line
              append-inner-icon="tabler-search" clear-icon="tabler-x"/>
          </div>
        </div>

        <VCol cols="4" sm="2">
          <AppSelect v-model="selectedType" placeholder="ប្រភេទ" :items="types"
            clearable @vue:updated="updateOptions"
            clear-icon="tabler-x"/>
        </VCol> -->

      </VCardText>

      <VDivider />

      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:model-value="selectedRows"
        v-model:page="page"
        :items="categories"
        item-value="id"
        :items-length="totalcategories"
        :headers="headers"
        class="text-secondary text-no-wrap"
        show-select
        @update:options="updateOptions"
      >
        <!-- # -->
        <template #item.id="{ item }">
          <div class="flex flex-col">
            <span class="text-primary">
              {{ item.id }}
            </span>
          </div>
        </template>

        <template #item.title="{ item }">
          <span class="text-base flex line-clamp-2 text-wrap max-w-[40ch]">
            {{ item.title }}
          </span>
        </template>


        <template #item.school_type="{ item }">
          <span class="text-base flex line-clamp-2 text-wrap max-w-[40ch]">
            {{ item.school_type_kh }}
          </span>
        </template>
        
        <template #item.questions_count="{ item }">
          <span class="text-base flex line-clamp-2 text-wrap max-w-[40ch]">
            {{ item.questions_count }} សំណួរ
          </span>
        </template>


        <!-- 👉 date -->
        <template #item.updated_at="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div class="text-body-1 text-secondary">
              {{ resolveDate(item.updated_at) }}
            </div>
          </div>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="isDialogVisible = !isDialogVisible; pending_delete_categories = item">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn :to="{ name: 'assessment-category-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="+itemsPerPage"
            :total-items="totalcategories"
          />
        </template>
      </VDataTableServer>
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
            <span class="opacity-70">តើអ្នកចង់លុប​​ <span class="text-red-500 font-medium opacity-100">{{ pending_delete_categories?.title }}</span> មែន?</span>
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
