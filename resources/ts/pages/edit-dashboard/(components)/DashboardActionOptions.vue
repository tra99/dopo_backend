<template>
  <VCard variant="outlined" min-width="300">
    <VCardTitle class="text-h6">
      ផ្លាស់ប្តូរ <span style="color:red">"{{ dashboard.title }}"</span>
    </VCardTitle>
    <v-divider></v-divider>
    <VList density="compact">
      <VListItem
        clickable
        v-for="item in menuOptions" 
          :key="item.value" 
          :prepend-icon="item.prependIcon"
          :disabled="item.disabled"
          @click="handleMenuAction(item.value)"
        >
        <VListItemTitle>{{ item.title }}</VListItemTitle>
      </VListItem>
    </VList>
  </VCard>
</template>
<script setup lang="ts">
import { ref } from 'vue';

// Props
const props = defineProps(['dashboard', 'isFirst', 'isLast'])
const emit = defineEmits(['delete', 'edit', 'move_down', 'move_up'])

// Menu options
// -- Delete action
const menuOptions = ref([
  { title: 'Delete', value: 'delete', prependIcon: 'tabler-trash', disabled: false, color: 'red' },
  { title: 'Edit', value: 'edit', prependIcon: 'tabler-edit', disabled: false, },
  { title: 'Move Up', value: 'move_up', prependIcon: 'tabler-arrow-up', disabled: props.isFirst, },
  { title: 'Move Down', value: 'move_down', prependIcon: 'tabler-arrow-down', disabled: props.isLast, },
])

const handleMenuAction = (action: string) => {
  switch (action) {
    case 'delete':
      emit('delete', props.dashboard)
      break
    case 'edit':
      emit('edit', props.dashboard)
      break
    case 'move_up':
      emit('move_up', props.dashboard)
      break
    case 'move_down':
      emit('move_down', props.dashboard)
      break
  }
}
</script>
