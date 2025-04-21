<template>
  <VCard variant="outlined" width="300px">
    <VCardTitle class="text-h6">
      ផ្លាស់ប្តូរ <span style="color:red">"{{ widget.title }}"</span>
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
const props = defineProps(['widget'])
const emit = defineEmits(['delete', 'edit', 'move_down', 'move_up'])

// Menu options
// -- Delete action
const menuOptions = ref([
  { title: 'Delete', value: 'delete', prependIcon: 'tabler-trash', disabled: false, color: 'red' },
  { title: 'Edit', value: 'edit', prependIcon: 'tabler-edit', disabled: false, },
])

const handleMenuAction = (action: string) => {
  switch (action) {
    case 'delete':
      emit('delete', props.widget)
      break
    case 'edit':
      emit('edit', props.widget)
      break
  }
}
</script>
