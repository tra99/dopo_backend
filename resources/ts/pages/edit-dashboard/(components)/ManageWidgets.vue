<template>
  <v-container v-if="dashboardId" style="height: 100%;">
    <div class="layout">
      <div v-if="dashboardDetail && dashboardDetail.dashboard_url" @click="viewDashboardOnMetabase(dashboardDetail)" class="btn-to-metabase">មើលលើ Metabase</div>
      <iframe v-if="dashboardDetail && dashboardDetail.dashboard_url"
        :src="dashboardDetail.dashboard_url"
        frameborder="0"
        width="100%"
        height="100%"
        allowtransparency
      ></iframe>
      <GridLayout v-else-if="widgets && widgets.length>0" :layout.sync="widgets" class="pa-0"
        :col-num="12"
        :row-height="30"
        :is-draggable="true"
        :is-resizable="true"
        :responsive="false"
        :vertical-compact="true"
        :use-css-transforms="true"
        @layout-updated="layoutUpdatedEvent"
        @layout-mounted="layoutMountedEvent"
      >
        <grid-item v-for="item in widgets"  v-bind="props" @contextmenu.prevent="toggleMenuOpen(item)" :key="item.id"
          :static="item.static"
          :x="item.x"
          :y="item.y"
          :w="item.w"
          :h="item.h"
          :i="item.i"
        >
          <VMenu v-model="menu[item.id]" location="end" @focusout="menu[item.id] = false" >
            <template #activator="{ props }">
              <div class="w-full h-full">
                <div v-if="isPreviewing" class="w-full h-full">
                  <div v-if="item.widget_url" @click="viewWidgetOnMetabase(item)" class="btn-to-metabase">មើលលើ Metabase</div>
                  <iframe v-if="item.widget_url" v-bind="props"
                    :src="item.widget_url"
                    frameborder="0"
                    width="100%"
                    height="100%"
                    allowtransparency
                  ></iframe>
                  <div class="w-full mt-5 flex justify-center flex-col items-center gap-5" v-else>
                    <img class="w-24" src="@images/icons/folder.png" />
                    <span>មិនមានអ្វីសំរាប់បង្ហាញទេ</span>
                  </div>
                </div>
                <v-container v-else v-bind="props">
                  <span class="text-md text-bold">{{item.title}}</span>
                  <v-divider class="mt-2 mb-2"></v-divider>
                  <div class="items-center d-flex justify-center">
                    <VIcon
                      class="blurred-icon"
                      size="60%"
                      :icon="statisticStub"
                    />
                  </div>
                </v-container>
              </div>
            </template>
            <WidgetActionOptions 
              @delete="confirmDeleteWidget" 
              @edit="handleEditWidget" 
              :widget="item"/>
          </VMenu>
        </grid-item>    
      </GridLayout>
      <div class="w-full my-15 flex justify-center flex-col items-center gap-5" v-else>
        <img class="w-24" src="@images/icons/folder.png" />
        <span>មិនមានអ្វីសំរាប់បង្ហាញទេ</span>
      </div>
    </div>
    <div v-if="widgets && widgets.length>0" style="position: absolute;top:5px;left:5px; opacity: 0.4;">
      <VBtn @click="togglePreview" color="warning" v-if="isPreviewing">កែប្រែ</VBtn>
      <VBtn @click="togglePreview" color="info" v-else>បង្ហាញទំរង់ពិត</VBtn>
    </div>
    <div v-if="dashboardDetail && dashboardDetail.dashboard_url == null" style="position: absolute;bottom:5px;left:5px; ">
      <a href="#" @click.prevent="handleAddWidget">
        <i class="tabler-layout-grid-add v-icon notranslate v-theme--light v-icon--size-default nav-item-icon ml-2 mr-2" aria-hidden="true"></i>
        <span class="nav-item-title">បន្ថែមផ្ទាំងទិន្ន័យ</span>
      </a>
    </div>
  </v-container>
  <v-container v-else>
    <div class="w-full my-15 flex justify-center flex-col items-center gap-5">
      <img class="w-24" src="@images/icons/folder.png" />
      <span>មិនមានអ្វីសំរាប់បង្ហាញទេ សូមជ្រើសរើសផ្ទាំងទិន្ន័យសិន</span>
    </div>
  </v-container>

  <!-- Widget Form Dialog -->
  <VDialog v-model="isDialogVisible" max-width="600">
    <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />
    <VCard>
      <template #title>
        <span class="text-h5 text-secondary">
          <span v-if="editingWidget.id">ផ្លាស់ប្តូរពត័មានហ្វីដចេត</span>
          <span v-else>បង្កើតហ្វីដចេត</span>
        </span>
        <v-divider class="mt-2 mb-3"></v-divider>
      </template>
      <VCardText class="py-3">
        <VRow>
          <VCol cols="12" class="py-1">
            <AppSelect
                :items="availableDashboards"
                :clearable="true"
                v-model="editingWidget.dashboard_id"
                label="ស្ថិតនៅក្រោមផ្ទាំងទិន្ន័យ"
                placeholder="មិនមាន"
            />
          </VCol>
          <VCol cols="12" class="py-1">
            <AppTextField
              v-model="editingWidget.title"
              label="ឈ្មោះហ្វីដចេត"
              :rules="[requiredValidator]"
              placeholder="សូមបញ្ចូលឈ្មោះហ្វីដចេត"
            />
          </VCol>
          <VCol cols="12" class="py-1">
            <AppTextField
              v-model="editingWidget.question"
              label="Metabase Question's ID"
              placeholder="សូមបញ្ចូល Metabase Question's ID"
            />
          </VCol>
          <VCol cols="12" class="py-1">
            <AppTextarea
              v-model="editingWidget.params"
              label="Metabase Question's Parameters"
              placeholder="សូមបញ្ចូល Metabase Question's Parameters"
            />
          </VCol>
          <VCol cols="12" class="pt-1 pb-1">
            <AppTextarea
              v-model="editingWidget.description"
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
  <!-- Confirm Delete Dialog -->
  <ConfirmDialog
    v-model:is-dialog-visible="isConfirmDialogVisible"
    cancel-title="បានបោះបង់ចោល"
    confirm-title="បានលុប"
    confirm-msg="ហ្វីដចេតត្រូវបានលុបដោយជោគជ័យ."
    :confirmation-question="'តើអ្នកពិតជាចង់លុបហ្វីដចេត (' + widgetToDelete.title + ') មែនទេ?'"
    cancel-msg="ហ្វីដចេតមិនត្រូវបានលុបទេ!!"
    @confirm="deleteDashboard(widgetToDelete, $event)"
  >
    <template v-slot:confirm-btn>បញ្ជាក់</template>
    <template v-slot:cancel-btn>បោះបង់ចោល</template>
  </ConfirmDialog>
</template>
<script lang="ts" setup> 
import { useGlobalStore } from '@core/stores/global';
import statisticStub from "@images/icons/statistic.svg";
import { watch } from "vue";
import { GridItem, GridLayout } from "vue3-grid-layout-next";
import WidgetActionOptions from './WidgetActionOptions.vue';

const props = defineProps(['dashboardId', 'isPreviewing'])
const emit = defineEmits(['preview'])
const widgets = ref<any>([])
const globalStore = useGlobalStore()

// const layout = reactive([]) // will cause some bug
// it will work, when responsive is false
// const layout = reactive([])

const errors = ref<any>({})
const isDialogVisible = ref(false)
const availableDashboards = ref<any>([])
const dashboardDetail = ref<any>({})
const editingWidget = ref({
  id: null,
  dashboard_id: null,
  title: null,
  axis_x: 0,
  axis_y: 0,
  width: 4,
  height: 4,
  sort: 0,
  description: null,
  question: null,
  params:null,
  component: null,
})

// Preview mode
const isPreviewing = ref(false)
function togglePreview() {
  isPreviewing.value = !isPreviewing.value
  emit('preview', isPreviewing.value)
}

async function loadWidgetsData(dashboard_id: number) {
  globalStore.setIsDialogVisible(true)
    const newWidgets = await $api('/v1/widgets' , {
      method: 'GET',
      query: { dashboard_id}
    })
    widgets.value = newWidgets.map((nw: any) => {
      return {
        x: nw.axis_x,
        y: nw.axis_y,
        w: nw.width,
        h: nw.height,
        i: nw.id,
        title: nw.title,
        sort: nw.sort,
        component: nw.component,
        static: nw.static? true: false,
        id: nw.id,
        dashboard_id: nw.dashboard_id,
        description: nw.description,
        question: nw.question,
        widget_url: nw.widget_url,
        widget_edit_url: nw.widget_edit_url,
        params: nw.params,
      }
    })
    isFirstLoaded = true
    globalStore.setIsDialogVisible(false)
}
async function loadDashboardsData() {
  const query = {}
  const { data: newDashboardsData, execute } = await useApi<any>(createUrl('/v1/dashboards/children', { query }))
  availableDashboards.value = newDashboardsData.value.map((item: any) => {
    return {
      title: item.title,
      value: item.id,
      dashboard_type: item.dashboard_type
    }
  })
  execute()
}
async function loadDashboardDetail(dashboardId: number) {
  const query = {}
  const { data: newDashboardDetail, execute } = await useApi<any>(createUrl(`/v1/dashboards/${dashboardId}`, { query }))
  dashboardDetail.value = newDashboardDetail.value
  execute()
}
onMounted(async () => {
  await loadDashboardsData()
});

// Load widgets on dashboardId changed
async function saveWidget(){
  const requestBody = {
    dashboard_id: editingWidget.value.dashboard_id,
    title: editingWidget.value.title,
    axis_x: editingWidget.value.axis_x,
    axis_y: editingWidget.value.axis_y,
    width: editingWidget.value.width,
    height: editingWidget.value.height,
    sort: editingWidget.value.sort,
    description: editingWidget.value.description,
    question: editingWidget.value.question,
    params: editingWidget.value.params,
    component: editingWidget.value.component,
  }
  try {
    if (editingWidget.value.id) {
      await $api(`/v1/widgets/${editingWidget.value.id}`, {
        method: 'PATCH',
        body: requestBody,
        onResponseError({ response }) {
          errors.value = response._data.errors
        },
      })
    } else {
      await $api('/api/v1/widgets', {
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
async function performSubmission() {
  globalStore.setIsDialogVisible(true)
  await saveWidget()
  globalStore.setIsDialogVisible(false)
  isDialogVisible.value = false;
  await loadWidgetsData(props.dashboardId)
}
function handleAddWidget(widget: any) {
  // 1. Set default value for the first widget
  const w = 4
  const h = 4
  let sort = 0
  let x = 0
  let y = 0
  // 2. Get the last widget
  const lastWidget = widgets.value[widgets.value.length - 1]
  if (lastWidget) {
    // 2.1. Get the last widget's x, y, w, h
    sort = lastWidget.sort + 1
    x = lastWidget.x + lastWidget.w
    y = lastWidget.y
    if (x + w > 12) {
      x = 0
      y = lastWidget.y + lastWidget.h
    }
  }
  // 3. Create a new widget
  editingWidget.value = {
    id: null,
    dashboard_id: props.dashboardId,
    title: null,
    axis_x: x,
    axis_y: y,
    width: w,
    height: h,
    sort,
    description: null,
    question: null,
    params: null,
    component: null,
  }
  isDialogVisible.value = true
}
watch(() => props.dashboardId, async (newDashboardId) => {
  if (newDashboardId != undefined || newDashboardId != null) {
    // 1. Load dashboard data
    await loadDashboardDetail(newDashboardId)
    if (!dashboardDetail.value.dashboard_url) {
      // 2. Load widgets of this dashboard if needed
      await loadWidgetsData(newDashboardId)
    }
  }
})

// Hadle widget resize
let isFirstLoaded = false
function layoutMountedEvent(layout: any){
    console.log("Mounted layout: ", layout)
    isFirstLoaded = true
}
async function layoutUpdatedEvent(newLayout: any){
    console.log("Updated layout: ", newLayout)
    if (isFirstLoaded) {
      isFirstLoaded = false
      return
    }
    globalStore.setIsDialogVisible(true)
    const response = await $api(`/api/v1/widgets`, {
      method: 'PATCH',
      body: {
        widgets: newLayout.map((item: any) => {
          return {
            id: item.i,
            axis_x: item.x,
            axis_y: item.y,
            width: item.w,
            height: item.h,
            sort: item.sort,
            dashboard_id: item.dashboardId,
            description: item.description,
            question: item.question,
            widget_url: item.widget_url,
            widget_edit_url: item.widget_edit_url,
            params: item.params,
            component: item.component,
          }
        })
      },
      onResponseError({ response }) {
        console.error(response)
      }
    })
    if (response.widgets) {
      widgets.value = response.widgets.map((nw: any) => {
        return {
          x: nw.axis_x,
          y: nw.axis_y,
          w: nw.width,
          h: nw.height,
          i: nw.id,
          title: nw.title,
          sort: nw.sort,
          component: nw.component,
          static: nw.static? true: false,
          id: nw.id,
          dashboard_id: nw.dashboard_id,
          description: nw.description,
          question: nw.question,
          params: nw.params,
        }
      })
      isFirstLoaded = true
    }
    globalStore.setIsDialogVisible(false)
}
// Context Menu on widget
// -- Toggle Menu Open
const menu = ref({})
function toggleMenuOpen(item: any) {
  menu.value[item.id] = !menu.value[item.id]
}
// -- Delete Widget
const isConfirmDialogVisible = ref(false)
const widgetToDelete = ref({title:'n/a'})
function confirmDeleteWidget(widget: any) {
  isConfirmDialogVisible.value = true
  widgetToDelete.value = widget
}
async function deleteDashboard(toDelete: any, isConfirmed: boolean) {
  isConfirmDialogVisible.value = false
  if (isConfirmed) {
    await $api(`/api/v1/widgets/${toDelete.id}`, {
      method: 'DELETE',
      onResponseError({ response }) {
        console.error(response)
      }
    })
    await loadWidgetsData(props.dashboardId)
  }
}
// -- Edit Widget
function handleEditWidget(widget: any) {
  editingWidget.value = {
    id: widget.id,
    dashboard_id: widget.dashboard_id,
    title: widget.title,
    axis_x: widget.x,
    axis_y: widget.y,
    width: widget.w,
    height: widget.h,
    sort: widget.sort,
    description: widget.description,
    question: widget.question,
    params: widget.params,
    component: widget.component,
  }
  isDialogVisible.value = true
}

// Visualize on Metabase
function viewDashboardOnMetabase(dashboard: any) {
  console.log(dashboard)
  if (dashboard.dashboard_edit_url) {
    window.open(dashboard.dashboard_edit_url, '_blank')
  } else {
    // globalStore.showError('មិនមាន URL សំរាប់បង្ហាញទេ')
    console.log('មិនមាន URL សំរាប់បង្ហាញទេ')
  }
}
function viewWidgetOnMetabase(widget: any) {
  console.log(widget)
  if (widget.widget_edit_url) {
    window.open(widget.widget_edit_url, '_blank')
  } else {
    console.log('មិនមាន URL សំរាប់បង្ហាញទេ')
  }
}
</script>
<style scoped>
/* .vue-grid-layout {
    background: #eee;
} */

.vue-grid-item {
  touch-action: none;
}
.vue-grid-item:not(.vue-grid-placeholder) {
    box-shadow: 0 3px 12px rgba(var(--v-shadow-key-umbra-color), var(--v-shadow-md-opacity)), 0 0 transparent, 0 0 transparent;
    background: rgb(var(--v-theme-surface));
    color: rgba(var(--v-theme-on-surface), var(--v-medium-emphasis-opacity));
    display: block;
    overflow: hidden;
    overflow-wrap: break-word;
    position: relative;
    padding: 0;
    text-decoration: none;
    transition-duration: .28s;
    transition-property: box-shadow, opacity;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    z-index: 0;
    border-color: rgba(var(--v-border-color), var(--v-border-opacity));
    border-style: solid;
    border-width: 0;
    border-radius: 6px;
}

.vue-grid-item .resizing {
    opacity: 0.9;
}

.vue-grid-item .static {
    background: #cce;
}

.vue-grid-item .text {
    font-size: 24px;
    text-align: center;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
    height: 100%;
    width: 100%;
}

.vue-grid-item .no-drag {
    height: 100%;
    width: 100%;
}

.vue-grid-item .minMax {
    font-size: 12px;
}

.vue-grid-item .add {
    cursor: pointer;
}

.vue-draggable-handle {
    position: absolute;
    width: 20px;
    height: 20px;
    top: 0;
    left: 0;
    background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10'><circle cx='5' cy='5' r='5' fill='#999999'/></svg>") no-repeat;
    background-position: bottom right;
    padding: 0 8px 8px 0;
    background-repeat: no-repeat;
    background-origin: content-box;
    box-sizing: border-box;
    cursor: pointer;
}

.layout{
    height: 100%;
}

.columns {
    -moz-columns: 120px;
    -webkit-columns: 120px;
    columns: 120px;
}
.blurred-icon {
  filter: blur(1px);
  opacity: 0.3;
}
.btn-to-metabase {
  position: absolute;
  left: 0px;
  top: 0px;
  background-color: #f5f5f5;
  opacity: 0.8;
  padding: 5px;
  margin: 5px;
  cursor: pointer;
}

</style>
