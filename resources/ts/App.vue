<script setup lang="ts">
import { useGlobalStore } from '@/@core/stores/global'
import { useSnackbarStore } from '@/@core/stores/snackbar'
import ScrollToTop from '@core/components/ScrollToTop.vue'
import initCore from '@core/initCore'
import { initConfigStore, useConfigStore } from '@core/stores/config'
import { hexToRgb } from '@core/utils/colorConverter'
import { useTheme } from 'vuetify'

const snackbarStore = useSnackbarStore();
const { isOpen, message, color } = storeToRefs(snackbarStore);
const { global } = useTheme()

// ℹ️ Sync current theme with initial loader theme
initCore()
initConfigStore()

const configStore = useConfigStore()

const globalStore = useGlobalStore();
const { isDialogVisible } = storeToRefs(globalStore);
</script>

<template>
  <VLocaleProvider :rtl="configStore.isAppRTL">
    <!-- ℹ️ This is required to set the background color of active nav link based on currently active global theme's primary -->
    <VApp :style="`--v-global-theme-primary: ${hexToRgb(global.current.value.colors.primary)}`">
      <VDialog
        v-model="isDialogVisible"
        width="300"
      >
        <VCard
          color="primary"
          width="300"
        >
          <VCardText class="pt-3">
            កំពុងដំណើរការ...
            <VProgressLinear
              indeterminate
              bg-color="rgba(var(--v-theme-surface), 0.1)"
              :height="8"
              class="mb-0 mt-4"
            />
          </VCardText>
        </VCard>
      </VDialog>
      <RouterView />

      <ScrollToTop />
      <VSnackbar transition="fade-transition" v-model="isOpen" :color="color" location="bottom end" variant="tonal">{{ message }}</VSnackbar>
    </VApp>
  </VLocaleProvider>
</template>
