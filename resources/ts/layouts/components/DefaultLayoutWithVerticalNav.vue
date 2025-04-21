<script lang="ts" setup>
import initializeNavigators from '@/navigation/vertical';

import NavBarNotifications from '@/layouts/components/NavBarNotifications.vue';
import NavSearchBar from '@/layouts/components/NavSearchBar.vue';
import UserProfile from '@/layouts/components/UserProfile.vue';
import Footer from './Footer.vue';

// @layouts plugin
import { VerticalNavLayout } from '@layouts';

const navItems = ref<any[]>([])

// Wait for the navigators to be initialized
onMounted(async () => {
  navItems.value = await initializeNavigators()
  console.log('Navigators initialized', navItems.value)
})
</script>

<template>
  <VerticalNavLayout :nav-items="navItems">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn id="vertical-nav-toggle-btn" class="ms-n3 d-lg-none" @click="toggleVerticalOverlayNavActive(true)">
          <VIcon size="26" icon="tabler-menu-2"/>
        </IconBtn>
        
        <NavSearchBar class="ms-lg-n3" />
        <!-- <NavbarThemeSwitcher /> -->
        <VSpacer />
        <!-- <NavBarI18n
          v-if="themeConfig.app.i18n.enable && themeConfig.app.i18n.langConfig?.length"
          :languages="themeConfig.app.i18n.langConfig"
        /> -->
        <NavBarNotifications class="me-1" />
        <UserProfile />
      </div>
    </template>

    <!-- 👉 Pages -->
    <slot />
    <!-- 👉 Footer -->
    <template #footer>
      <Footer class="pa-4 opacity-70 flex flex-1 items-end"/>
    </template>
    <!-- <TheCustomizer /> -->
  </VerticalNavLayout>
</template>
