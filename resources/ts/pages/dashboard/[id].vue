<template>
  <div class="layout">
    <iframe v-if="dashboardDetail && dashboardDetail.dashboard_url"
      :src="dashboardDetail.dashboard_url"
      frameborder="0"
      width="100%"
      style="flex:1"
      allowtransparency
    ></iframe>
    <GridLayout v-else-if="widgets && widgets.length>0" :layout.sync="widgets" class="pa-0"
      :col-num="12"
      :row-height="30"
      :is-draggable="false"
      :is-resizable="false"
      :responsive="false"
      :vertical-compact="true"
      :use-css-transforms="true"
    >
      <grid-item v-for="item in widgets"  :key="item.id"
        :static="item.static"
        :x="item.x"
        :y="item.y"
        :w="item.w"
        :h="item.h"
        :i="item.i"
      >
        <iframe v-if="item.widget_url"
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
      </grid-item>    
    </GridLayout>
    <div class="w-full my-15 flex justify-center flex-col items-center gap-5" v-else>
      <img class="w-24" src="@images/icons/folder.png" />
      <span>មិនមានអ្វីសំរាប់បង្ហាញទេ</span>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { router } from '@/plugins/1.router';
import { useGlobalStore } from '@core/stores/global';
import { watch } from "vue";
import { GridItem, GridLayout } from "vue3-grid-layout-next";

const widgets = ref<any>([])
const globalStore = useGlobalStore()
const dashboardDetail = ref<any>({})

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
      params: nw.params,
    }
  })
  globalStore.setIsDialogVisible(false)
}
async function loadDashboardDetail(dashboardId: number) {
  const query = {}
  const { data: newDashboardDetail, execute } = await useApi<any>(createUrl(`/v1/dashboards/${dashboardId}`, { query }))
  dashboardDetail.value = newDashboardDetail.value
  execute()
}

// const params: any = ref(router.currentRoute.value.params)
watch(() => router.currentRoute.value.params, async (params) => {
  const newDashboardId = params['id']
  console.log('newDashboardId', newDashboardId)
  if (newDashboardId != undefined || newDashboardId != null) {
    // 1. Load dashboard data
    await loadDashboardDetail(newDashboardId)
    if (!dashboardDetail.value.dashboard_url) {
      await loadWidgetsData(parseInt(newDashboardId as string))
    }
  }
}, { deep: true, immediate: true })

</script>

<style scoped>
.layout {
  min-height: calc(100vh - 170px);
  display: flex;
  flex-direction: column;
  /* margin-left: -10px;
  margin-right: -10px; */
}
.vue-grid-item {
  touch-action: none;
}
.vue-grid-layout {
  margin-left: -10px;
  margin-right: -10px;
}
.page-content-container {
  height: 100%;
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
    padding: 0px;
    background-repeat: no-repeat;
    background-origin: content-box;
    box-sizing: border-box;
    cursor: pointer;
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

</style>
