<script lang="ts" setup>
import { router } from '@/plugins/1.router'

onMounted(() => {
  querySelections(' ')
})

const form = ref({
  name: '',
  email: '',
  password: '',
  status: false,
  school_id: null,
  roles: [],
  description: '',

})

// for create role dialog
const isDialogVisible = ref(false)
const new_role = ref('')

const { data: rolesData, execute } = await useApi<any>(createUrl('/v1/roles'))

const roles = computed((): any[] => rolesData.value?.data)
const schools = ref([])

async function handleSubmit() {
  const res = await $api('/v1/users', {
    method: 'POST',
    body: {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      status: form.value.status,
      school_id: form.value.school_id,
      ...(form.value.description? { description: form.value.description }: null ), 
      roles: JSON.stringify(form.value.roles) },
  })

  router.replace('/user')
}

// related to autocomplete school input
const school_loading = ref(false)
const school_search = ref()

let timeout: ReturnType<typeof setTimeout> | null = null

const querySelections = (query: string) => {
  if (timeout)
    clearTimeout(timeout) // Clear previous timeout to debounce
  school_loading.value = true

  // Simulated ajax query
  timeout = setTimeout(async () => {
    try {
      const { data: schoolData } = await useApi<any>(
        createUrl(`/v1/schools?page=1&limit=25&sort_direction=asc&search=${query}`),
      )

      schools.value = schoolData.value?.data || []
    }
    catch (error) {
      console.error('Error fetching schools:', error)
    }
    finally {
      school_loading.value = false
    }
  }, 500)
}

async function handle_createrole() {
  const res = await $api('/v1/roles', {
    method: 'POST',
    body: {
      name: new_role.value,
    },
  })

  isDialogVisible.value = false
  execute()
}
</script>

<template>
  <VCard>
    <VCol>
      <VForm @submit.prevent.left="() => { handleSubmit() }">
        <VRow>
          <!-- 👉 ឈ្មោះអ្នកប្រើប្រាស់ -->
          <VCol
            cols="12"
            md="6"
          >
            <AppTextField
              v-model="form.name"
              label="ឈ្មោះអ្នកប្រើប្រាស់"
              placeholder="សូមបញ្ចូលឈ្មោះអ្នកប្រើប្រាស់"
              :rules="[requiredValidator]"
            />
          </VCol>

          <!-- 👉 តួនាទី -->
          <VCol
            cols="12"
            md="6"
            class="flex gap-2 items-end"
          >
            <AppSelect
              v-model="form.roles"
              :items="roles"
              item-title="name"
              :loading="school_loading"
              item-value="id"
              placeholder="ជ្រើសរើសតួនាទី"
              clearable
              label="តួនាទី"
              chips
              multiple
              closable-chips
              :rules="[requiredValidator]"
            />
            <!-- Dialog Activator -->
            <VBtn
              icon="tabler-plus"
              variant="tonal"
              color="primary"
              rounded
              @click="isDialogVisible = !isDialogVisible"
            />
          </VCol>

          <!-- 👉 Email -->
          <VCol
            cols="12"
            md="6"
          >
            <AppTextField
              v-model="form.email"
              label="អុីម៉េល"
              :rules="[requiredValidator, emailValidator]"
              placeholder="ឧ. name@email.com"
            />
          </VCol>
          <!-- 👉 អក្សរសំងាត់ -->
          <VCol
            cols="12"
            md="6"
          >
            <AppTextField
              v-model="form.password"
              label="អក្សរសំងាត់"
              :rules="[requiredValidator, passwordValidator]"
              placeholder="យ៉ាងតិច៦ខ្ទង់"
            />
          </VCol>

          <!-- 👉 school id  -->
          <VCol
            cols="12"
            md="6"
          >
            <AppAutocomplete
              v-model:search="school_search"
              v-model="form.school_id"
              label="សាលារៀន"
              :items="schools"
              :item-title="(school: any) => `${school?.school_name_kh} (${school?.school_name_en})`"
              item-value="id"
              placeholder="ជ្រើសរើសាសលារៀន"
              :menu-props="{ maxHeight: '200px' }"
              :rules="[requiredValidator]"
              clearable
            >
              <template #item="{ props, item }">
                <VListItem
                  v-bind="props"
                  :title="item?.raw?.school_name_kh"
                  :subtitle="item.raw?.school_name_en"
                />
              </template>
            </AppAutocomplete>
          </VCol>

          <!-- 👉 ដាក់អោយដំណើរការ -->
          <VCol
            cols="12"
            class="flex gap-2 items-center"
          >
            <VSwitch v-model="form.status" />
            <span class="text-secondary">ដាក់អោយដំណើរការ</span>
          </VCol>

          <VCol cols="12">
            <TiptapEditor
              v-model="form.description"
              placeholder="សេចក្តីចំណាំផ្សេងៗ អាចដាក់នៅទីនេះបាន"
              class="border rounded basic-editor"
            />
          </VCol>

          <VCol
            cols="12"
            class="d-flex flex-row-reverse gap-4"
          >
            <VBtn type="submit">
              រក្សាទុក
            </VBtn>
            <VBtn
              to="/user"
              type="reset"
              color="secondary"
              variant="tonal"
            >
              បោះបង់ចោល
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCol>
  </VCard>

  <!-- Create role form dialog -->
  <VDialog
    v-model="isDialogVisible"
    max-width="600"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />
    <!-- Dialog Content -->
    <VCard>
      <VCardTitle>
        <span class="text-xl text-secondary">សូមបញ្ចាក់</span>
      </VCardTitle>
      <VCardText>
        <VRow>
          <VCol cols="12">
            <AppTextField
              v-model="new_role"
              label="ឈ្មោះតួនាទី"
              :rules="[requiredValidator]"
              placeholder="សូមបញ្ចូលឈ្មោះតួនាទី"
            />
          </VCol>
        </VRow>
      </VCardText>

      <VCardText class="d-flex justify-end flex-wrap gap-3">
        <VBtn
          variant="tonal"
          color="secondary"
          @click="isDialogVisible = false"
        >
          បោះបង់ចោល
        </VBtn>
        <VBtn
          color="primary"
          @click="handle_createrole"
        >
          រក្សាទុក
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
