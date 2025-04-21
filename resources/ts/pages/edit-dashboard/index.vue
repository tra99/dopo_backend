<template>
  <div class="edit-dashboard-page">
    <VCard variant="outlined" class="dashboard-card">
      <ManageWidgets @preview="handlePreviewMode" :dashboardId="selectedDashboarId"/>
    </VCard>
    <ManageMenu v-show="isEditing" class="manage-menu" @select="handleSelectedDashboard"/>
  </div>
</template>
<script lang="ts" setup>
import ManageMenu from './(components)/ManageMenu.vue';
import ManageWidgets from './(components)/ManageWidgets.vue';

const selectedDashboarId = ref<number>()
function handleSelectedDashboard(dashboardId: number) {
  selectedDashboarId.value = dashboardId
}

const isEditing = ref<boolean>(true)
function handlePreviewMode() {
  isEditing.value = !isEditing.value
}
</script>
<style lang="scss">
  .edit-dashboard-page {
    width: 100%;
    display: flex;
    flex-direction: row;
    height: 100%;
  }
  .manage-menu {
    width: 300px;
    height: 100%;
    overflow-y: auto;
  }
  .dashboard-card {
    flex: 1;
    min-height: calc(100vh - 170px);
  }
</style>
