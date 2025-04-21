<script lang="ts" setup>
import { computed, ref } from 'vue';
import positionTable from './(components)/position-table.vue';
import userTable from './(components)/user-table.vue';

const currentTab = ref('window1')

// Map tabs to their respective components
const tabComponents: Record<string, any> = {
  window1: userTable, // First tab renders userTable
  window2: positionTable, // Second tab has no component
}

// Computed property to dynamically return the current component
const activeComponent = computed(() => tabComponents[currentTab.value])
</script>

<template>
  <!-- Tabs -->
  <VTabs v-model="currentTab" class="v-tabs-pill mb-2" align-tabs="end">
    <VTab value="window1">
      <VIcon icon="tabler-users" class="mb-1 mr-2" />
      <span> អ្នកប្រើប្រាស់ </span>
    </VTab>
    <VTab value="window2">
      <VIcon icon="tabler-lock-access" class="mb-1 mr-2" />
      <span> តួនាទី </span>
    </VTab>
  </VTabs>

  <!-- Dynamic Component Rendering -->
  <div class="p-4 drop-shadow-lg">
    <VTabsWindow v-model="currentTab">
      <VTabsWindowItem :value="currentTab">
        <component v-if="activeComponent" :is="activeComponent" />
      </VTabsWindowItem>
    </VTabsWindow>
  </div>
</template>
