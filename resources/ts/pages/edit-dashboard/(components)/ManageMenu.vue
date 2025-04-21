<template>
    <div class="layout-nav-type-vertical">
      <div class="layout-vertical-nav custom-nav">
        <ul class="nav-items">
          <template v-if="dashboardsData && Object.keys(dashboardsData).length>0" v-for="(dashboard, group) in dashboardsData">
            <li class="nav-section-title text-secondary">
              <div class="title-wrapper text-secondary opacity-40">
                <span class="title-text" icon="tabler-minus">{{ group }}</span>
              </div>
            </li>
            <template v-for="(item, index) in dashboard">
              <li v-if="item.children && item.children.length>0" class="nav-group text-secondary" :class="[
                  {
                    active: groupActive == item.id,
                    open: groupOpen == item.id,
                    disabled: item.disable,
                  },
                ]">
                <VMenu v-model="menu[item.id]" location="bottom" @focusout="menu[item.id] = false">
                  <template #activator="{ props }">
                    <div v-bind="props" class="nav-group-label" @click.prevent="toggleGroupOpen(item.id)" @contextmenu.prevent="toggleMenuOpen(item)" :class="'menu-item-'+item.id">
                      <i class="tabler-float-right v-icon notranslate v-theme--light v-icon--size-default nav-item-icon" aria-hidden="true"></i>
                      <span class="nav-item-title">{{ item.title }}</span>
                      <i class="tabler-chevron-right v-icon notranslate v-theme--light nav-group-arrow" aria-hidden="true" style="font-size: 20px; height: 20px; width: 20px;"></i>
                    </div>
                  </template>
                  <DashboardActionOptions 
                    @delete="confirmDeleteDashboard" 
                    @edit="handleEditDashboard" 
                    @move_down="moveDownDashboard"
                    @move_up="moveUpDashboard"
                    :dashboard="item"/>
                </VMenu>
                <TransitionExpand>
                  <ul v-show="groupOpen==item.id" class="nav-group-children">
                    <li v-for="(children,childIndex) in item.children" class="nav-link">
                      <VMenu v-model="menu[children.id]" location="bottom" @focusout="menu[children.id] = false">
                        <template #activator="{ props }">
                          <a v-bind="props" href="#" :class="{'router-link-active router-link-exact-active': groupActive == children.id}" @click="toggleGroupActive(children.id)" @contextmenu.prevent="toggleMenuOpen(children)">
                            <i class="tabler-circle v-icon notranslate v-theme--light v-icon--size-default nav-item-icon text-secondary" aria-hidden="true"></i>
                            <span class="nav-item-title">{{ children.title }}</span>
                          </a>
                        </template>
                        <DashboardActionOptions 
                          @delete="confirmDeleteDashboard" 
                          @edit="handleEditDashboard" 
                          @move_down="moveDownDashboard"
                          @move_up="moveUpDashboard"
                          :isFirst="childIndex==0"
                          :isLast="childIndex==item.children.length-1"
                          :dashboard="children"/>
                      </VMenu>
                    </li>
                  </ul>
                </TransitionExpand>
              </li>
              <li v-else class="nav-link text-secondary">
                <VMenu v-model="menu[item.id]" location="bottom" @focusout="menu[item.id] = false">
                  <template #activator="{ props }">
                    <a v-bind="props" aria-current="page" href="#" :class="{'router-link-active router-link-exact-active': groupActive == item.id}" @click="toggleGroupActive(item.id)" @contextmenu.prevent="toggleMenuOpen(item)">
                      <i :class="'tabler-'+item.icon" class="v-icon notranslate v-theme--light v-icon--size-default nav-item-icon text-secondary" aria-hidden="true"></i>
                      <span class="nav-item-title">{{ item.title }}</span>
                      <i @click.prevent="handleAddDashboard(item)" class="tabler-new-section v-icon notranslate v-theme--light nav-group-arrow" aria-hidden="true" style="font-size: 20px; height: 20px; width: 20px;"></i>
                    </a>
                  </template>
                  <DashboardActionOptions 
                    @delete="confirmDeleteDashboard" 
                    @edit="handleEditDashboard" 
                    @move_down="moveDownDashboard"
                    @move_up="moveUpDashboard"
                    :isFirst="index==0"
                    :isLast="index==dashboard.length-1"
                    :dashboard="item"/>
                </VMenu>
              </li>
            </template>
          </template>
          <li class="nav-section-title text-secondary">
            <div class="title-wrapper text-secondary opacity-40">
                <a href="#" @click.prevent="handleAddDashboard(null)"><button>+ ផ្ទាំងពត័មានថ្មី</button></a>
              </div>
          </li>
        </ul>

        <!-- Delete dashboard confirmation -->
        <ConfirmDialog
          v-model:is-dialog-visible="isConfirmDialogVisible"
          cancel-title="បានបោះបង់ចោល"
          confirm-title="បានលុប"
          confirm-msg="ផ្ទាំងទិន្ន័យត្រូវបានលុបដោយជោគជ័យ."
          :confirmation-question="'តើអ្នកពិតជាចង់លុបផ្ទាំងទិន្ន័យ (' + dashboardToDelete.title + ') មែនទេ?'"
          cancel-msg="ផ្ទាំងទិន្ន័យមិនត្រូវបានលុបទេ!!"
          @confirm="deleteDashboard(dashboardToDelete, $event)"
        >
          <template v-slot:confirm-btn>បញ្ជាក់</template>
          <template v-slot:cancel-btn>បោះបង់ចោល</template>
        </ConfirmDialog>

        <!-- Dialog form for add and edit -->
        <VDialog v-model="isDialogVisible" max-width="600">
          <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />
          <VCard>
            <template #title>
              <span class="text-h5 text-secondary">
                <span v-if="editingDashboard.id">ផ្លាស់ប្តូរពត័មានផ្ទាំងទិន្ន័យ</span>
                <span v-else>បង្កើតផ្ទាំងទិន្ន័យ</span>
              </span>
              <v-divider class="mt-2 mb-3"></v-divider>
            </template>
            <VCardText class="py-3">
              <VRow>
                <VCol cols="12" class="py-1">
                  <AppSelect
                      :items="availableParentDashboards"
                      :clearable="true"
                      v-model="editingDashboard.parent_id"
                      label="ស្ថិតនៅក្រោមផ្ទាំងទិន្ន័យ"
                      placeholder="មិនមាន"
                  />
                </VCol>
                <VCol cols="12" class="py-1">
                  <AppTextField
                    v-model="editingDashboard.title"
                    label="ឈ្មោះផ្ទាំងទិន្ន័យ"
                    :rules="[requiredValidator]"
                    placeholder="សូមបញ្ចូលឈ្មោះផ្ទាំងទិន្ន័យ"
                  />
                </VCol>
                <VCol cols="12" class="py-1">
                  <AppSelect
                      :items="dashboardTypes"
                      :rules="[requiredValidator]"
                      v-model="editingDashboard.dashboard_type"
                      label="ក្រុម"
                      placeholder="ជ្រើសរើសក្រុម"
                  />
                </VCol>
                <VCol cols="12" class="py-1">
                  <AppTextField
                    v-model="editingDashboard.dashboard"
                    label="Metabase's Dashboard ID"
                    placeholder="សូមបញ្ចូល Metabase's Dashboard ID"
                  />
                </VCol>
                <VCol cols="12" class="py-1" v-if="!editingDashboard.parent_id">
                  <AppTextField
                    v-model="editingDashboard.icon"
                    label="រូបតំណាង (Tabler Icon) - https://icon-sets.iconify.design/tabler"
                    placeholder="ឧទារណ៍: tabler-chart-bar"
                  />
                </VCol>
                <VCol cols="12" class="pt-1 pb-1">
                  <AppTextarea
                    v-model="editingDashboard.description"
                    label="ពណ័នា"
                    placeholder="សូមបញ្ចូលពណ័នា"
                  />
                </VCol>
              </VRow>
            </VCardText>

            <VCardText class="d-flex justify-end flex-wrap gap-3 mt-5">
              <VBtn variant="tonal" color="secondary" @click="isDialogVisible = false">
                បោះបង់ចោល
              </VBtn>
              <VBtn color="primary" @click="performSubmission">
                រក្សាទុក
              </VBtn>
            </VCardText>
          </VCard>
        </VDialog>
      </div>
    </div>
</template>
<script setup lang="ts">
import { useGlobalStore } from '@core/stores/global'
import { TransitionExpand } from '@layouts/components'
import { ref, watch } from 'vue'
import DashboardActionOptions from './DashboardActionOptions.vue'
const emit = defineEmits(['select'])

// 👉 Fetching available menu
const dashboardsData = ref<any>({})
const availableParentDashboards = ref<any>([])
async function loadDashboardsData() {
  const query = {}
  const { data: newDashboardsData, execute } = await useApi<any>(createUrl('/v1/dashboards/tree', { query }))
  dashboardsData.value = newDashboardsData.value
  execute()

  // Fetch available parent dashboards
  const { data: newParentDashboardsData } = await useApi<any>(createUrl('/v1/dashboards/parents', { query }))
  availableParentDashboards.value = newParentDashboardsData.value.map((item: any) => {
    return {
      title: item.title,
      value: item.id,
      dashboard_type: item.dashboard_type
    }
  })
}
onMounted(async () => {
  await loadDashboardsData()
});

// Manage Menu
const menu = ref({})
let dashboardTypes = [
    { title: 'ពត័មានទូទៅ', value: 'ពត័មានទូទៅ' },
    { title: 'ការវាយតម្លៃសាលារៀន', value: 'ការវាយតម្លៃសាលារៀន' },
    { title: 'ការចុះគាំទ្រសាលារៀនគំរូ', value: 'ការចុះគាំទ្រសាលារៀនគំរូ' },
]
// -- Toggle Group Open
const groupOpen = ref(-1)
function toggleGroupOpen(itemId: any) {
  menu.value[itemId] = false
  if (groupOpen.value === itemId) {
    groupOpen.value = -1
  } else {
    groupOpen.value = itemId
  }
}
// -- Toggle Group Active
const groupActive = ref(-1)
function toggleGroupActive(itemId: any) {
  menu[itemId]=false
  groupActive.value = itemId
  emit('select', itemId)
}

// -- Toggle Menu Open
function toggleMenuOpen(item: any) {
  menu.value[item.id] = !menu.value[item.id]
}

// -- Handle delete dashboard
const isConfirmDialogVisible = ref(false)
const dashboardToDelete = ref({title:'n/a'})
function confirmDeleteDashboard(toDelete: any) {
  isConfirmDialogVisible.value = true
  dashboardToDelete.value = toDelete
}
async function deleteDashboard(toDelete: any, isConfirmed: boolean) {
  isConfirmDialogVisible.value = false
  if (isConfirmed) {
    await $api(`/api/v1/dashboards/${toDelete.id}`, {
      method: 'DELETE',
      onResponseError({ response }) {
        console.error(response)
      }
    })
    await loadDashboardsData()
  }
}

// Handle add and edit dashboard
const globalStore = useGlobalStore()
const isDialogVisible = ref(false)
const errors = ref<any>({})

const editingDashboard = ref({
  id: null,
  title: null,
  dashboard_type: null,
  description: null,
  icon: 'layout-dashboard',
  dashboard: null,
  parent_id: null,
  is_draggable: true,
  sort: 0
})
// -- Show edit dashboard form
function handleEditDashboard(dashboard: any) {
  editingDashboard.value = { ...dashboard }
  isDialogVisible.value = true
}
// -- Show add dashboard form
function handleAddDashboard(parentDashboard: any) {
  editingDashboard.value = {
    id: null,
    title: null,
    dashboard_type: null,
    description: null,
    icon: 'layout-dashboard',
    dashboard: null,
    is_draggable: true,
    sort: 0,
    parent_id: null,
  }
  if (parentDashboard) {
    editingDashboard.value.parent_id = parentDashboard.id
  }

  isDialogVisible.value = true
}
// -- Show loading, and refresh data after submission
async function performSubmission() {
  globalStore.setIsDialogVisible(true)
  await saveDashboard()
  globalStore.setIsDialogVisible(false)
  isDialogVisible.value = false;
  await loadDashboardsData()
}
// -- Create/Update dashboard data to backend server
async function saveDashboard(){
  const requestBody = {
    title: editingDashboard.value.title,
    dashboard_type: editingDashboard.value.dashboard_type,
    description: editingDashboard.value.description,
    icon: editingDashboard.value.icon,
    dashboard: editingDashboard.value.dashboard,
    is_draggable: editingDashboard.value.is_draggable,
    sort: editingDashboard.value.sort,
  }
  try {
    if (editingDashboard.value.parent_id) {
      requestBody['parent_id'] = editingDashboard.value.parent_id
    }
    if (editingDashboard.value.id) {
      await $api(`/v1/dashboards/${editingDashboard.value.id}`, {
        method: 'PATCH',
        body: requestBody,
        onResponseError({ response }) {
          errors.value = response._data.errors
        },
      })
    } else {
      await $api('/api/v1/dashboards', {
        method: 'POST',
        body: requestBody,
        onResponseError({ response }) {
          errors.value = response._data.errors
        },
      })
    }
  } catch (err) {
    console.error(err)
  }
}
// -- Watch when parent of editing dashboard is changed
let parentOrGroupIsChanging = false
// ---- When parent is changed, update dashboard type
watch(() => editingDashboard.value.parent_id, (newParentId) => {
  if (!parentOrGroupIsChanging) {
    parentOrGroupIsChanging = true
    const parentDashboard = availableParentDashboards.value.find((item: any) => item.value === newParentId)
    if (parentDashboard) {
      editingDashboard.value.dashboard_type = parentDashboard.dashboard_type;
      // --- Get children
      const parentDetail = dashboardsData.value[parentDashboard.dashboard_type].find((item: any) => item.id === newParentId)
      const children = parentDetail.children
      if (children && children.length > 0) {
        editingDashboard.value.sort = children[children.length - 1].sort + 1
      }
    }

    nextTick(() => {
      parentOrGroupIsChanging = false; // Reset after Vue updates
    });
  }
})
// ---- When group is changed, update parent dashboard
watch(() => editingDashboard.value.dashboard_type, (newDashboardType) => {
  if (!parentOrGroupIsChanging) {
    parentOrGroupIsChanging = true
    editingDashboard.value.parent_id = null
    nextTick(() => {
      parentOrGroupIsChanging = false; // Reset after Vue updates
    });
  }
})

// Handle move up and move down dashboard
// async function moveDownDashboard(dashboard: any) {
//   // -- let's find the dashboards in the same group
//   const dashboards = dashboardsData.value[dashboard.dashboard_type]
//   const currentDashboardIndex = dashboards.findIndex((item: any) => item.id === dashboard.id)
//   if (currentDashboardIndex === dashboards.length - 1) {
//     // this is already the last one, do nothing
//     return
//   } else {
//     globalStore.setIsDialogVisible(true)
//     const nextDashboard = dashboards[currentDashboardIndex + 1]
//     let currentSort = dashboard.sort
//     let nextSort = nextDashboard.sort

//     if (currentSort == nextSort) {
//       nextSort = nextSort + 1
//     }
//     // swap sort value beteween current and next dashboard
//     // -- now swap the sort value of selected dashboard
//     editingDashboard.value = { ...dashboard }
//     editingDashboard.value.sort = nextSort
//     await saveDashboard()
//     // -- now swap the sort value of next dashboard
//     editingDashboard.value = { ...nextDashboard }
//     editingDashboard.value.sort = currentSort
//     await saveDashboard()
//     await loadDashboardsData()
//     globalStore.setIsDialogVisible(false)
//   }
// }
async function moveDownDashboard(dashboard: any) {
  // -- let's find the dashboards in the same group
  const dashboards = dashboardsData.value[dashboard.dashboard_type]
  let nextDashboard = { 
    id: null,
    dashboard_type: null,
    title: null,
    description: null,
    icon: 'layout-dashboard',
    dashboard: null,
    parent_id: null,
    is_draggable: true,
    sort: 0
  }
  let currentDashboardIndex = -1
  if (dashboard.parent_id) {
    // find dashboard in the child group
    let parent = { children: []};
    for (const item of dashboards) {
      if (item.id === dashboard.parent_id) {
        parent = item
        break
      }
    }
    if (parent && parent.children.length>0) {
      currentDashboardIndex = parent['children'].findIndex((item: any) => item.id === dashboard.id)
      nextDashboard = parent['children'][currentDashboardIndex + 1]
    }
  } else {
    // Find dashboard in the parent group
    currentDashboardIndex = dashboards.findIndex((item: any) => item.id === dashboard.id)
    nextDashboard = dashboards[currentDashboardIndex + 1]
  }

  if (nextDashboard) {
    globalStore.setIsDialogVisible(true)
    
    let currentSort = dashboard.sort
    let nextSort = nextDashboard.sort

    if (currentSort == nextSort) {
      nextSort = nextSort + 1
    }
    // swap sort value beteween current and previous dashboard
    // -- now swap the sort value of selected dashboard
    editingDashboard.value = { ...dashboard }
    editingDashboard.value.sort = nextSort
    await saveDashboard()
    // -- now swap the sort value of previous dashboard
    editingDashboard.value = { ...nextDashboard }
    editingDashboard.value.sort = currentSort
    await saveDashboard()
    await loadDashboardsData()
    globalStore.setIsDialogVisible(false)
  } else {
    console.log('nextDashboard is null')
  }
}
async function moveUpDashboard(dashboard: any) {
  // -- let's find the dashboards in the same group
  const dashboards = dashboardsData.value[dashboard.dashboard_type]
  let previousDashboard = { 
    id: null,
    dashboard_type: null,
    title: null,
    description: null,
    icon: 'layout-dashboard',
    dashboard: null,
    parent_id: null,
    is_draggable: true,
    sort: 0
  }
  let currentDashboardIndex = -1
  if (dashboard.parent_id) {
    // find dashboard in the child group
    let parent = { children: []};
    for (const item of dashboards) {
      if (item.id === dashboard.parent_id) {
        parent = item
        break
      }
    }
    if (parent && parent.children.length>0) {
      currentDashboardIndex = parent['children'].findIndex((item: any) => item.id === dashboard.id)
      previousDashboard = parent['children'][currentDashboardIndex - 1]
    }
  } else {
    // Find dashboard in the parent group
    currentDashboardIndex = dashboards.findIndex((item: any) => item.id === dashboard.id)
    previousDashboard = dashboards[currentDashboardIndex - 1]
  }

  if (currentDashboardIndex <= 0) {
    // this is already the first one, or invalid index, so do nothing
    console.log('currentDashboardIndex is invalid', currentDashboardIndex)
    return
  } else if (previousDashboard) {
    globalStore.setIsDialogVisible(true)
    
    let currentSort = dashboard.sort
    let previousSort = previousDashboard.sort

    if (currentSort == previousSort) {
      previousSort = previousSort - 1
    }
    // swap sort value beteween current and previous dashboard
    // -- now swap the sort value of selected dashboard
    editingDashboard.value = { ...dashboard }
    editingDashboard.value.sort = previousSort
    await saveDashboard()
    // -- now swap the sort value of previous dashboard
    editingDashboard.value = { ...previousDashboard }
    editingDashboard.value.sort = currentSort
    await saveDashboard()
    await loadDashboardsData()
    globalStore.setIsDialogVisible(false)
  } else {
    console.log('previousDashboard is null')
  }
}
</script>
<style scoped>
  .custom-nav {
    inset-block-start: unset;
    inset-inline-start: unset; 
    box-shadow: none !important;
    background-color: transparent !important;
    width: 300px;
  }
  .nav-group .active {
    background: linear-gradient(270deg, rgba(var(--v-global-theme-primary), 0.7) 0%, rgb(var(--v-global-theme-primary)) 100%) !important;
    box-shadow: 0 2px 6px rgba(var(--v-global-theme-primary), 0.3);
  }
  @media (max-width: 1279px) {
    .custom-nav:not(.visible) {
        transform: none;
    }
  }
  @media (max-width: 1279px) {
      .custom-nav {
          transition: none;
      }
  }
</style>
