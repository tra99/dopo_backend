<script setup lang="ts">
// import { getFcmToken } from '../utils/firebaseMessenging'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import MoeysLogo from '@images/moeys-logo.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'

import { useSnackbarStore } from '@core/stores/snackbar'
import { themeConfig } from '@themeConfig'

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

const snackbar = useSnackbarStore()
const isSubmitting = ref(false)

const form = ref({
  email: '',
  password: '',
  remember: false,
})

const login = async () => {
  isSubmitting.value = true

  // const fcm_token = await getFcmToken()
  try {
    const res = await $api('/api/v1/auth/login', {
      method: 'POST',
      body: {
        email: form.value.email,
        password: form.value.password,
        remember_me: form.value.remember,
      },
    })

    isSubmitting.value = false

    const { accessToken, user } = res

    // useCookie('userData').value = userData
    useCookie('accessToken').value = accessToken
    useCookie('user').value = user

    // Redirect to `to` query if exist or redirect to index route
    // ❗ nextTick is required to wait for DOM updates and later redirect
    await nextTick(() => {
      window.location.href = '/';
    })
  }
  catch (err: any) {
    isSubmitting.value = false

    // snackbar.openSnackbar('ទិន្នន័យគណនីខុស' || err?.response?._data?.message,'error')
    snackbar.openSnackbar('ទិន្នន័យគណនីមិនត្រឹមត្រូវ', 'error')
  }
}

const isPasswordVisible = ref(false)

const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
</script>

<template>
  <VRow
    no-gutters
    class="auth-wrapper bg-surface"
  >
    <VCol
      md="8"
      class="d-none d-md-flex"
    >
      <div class="position-relative bg-background w-100 me-0">
        <div class="flex relative align-center justify-center w-100 h-100">
          <VImg
            max-width="200"
            :src="MoeysLogo"
            class="auth-illustration mb-2"
          />
          <div class="absolute font-bold text-center bottom-1/6 -translate-y-1/2 text-4xl tracking-wide text-secondary">
            ប្រព័ន្ធព័ត៌មានគ្រប់គ្រង <br> ការអនុវត្តស្តង់ដាសាលារៀនគំរូ
          </div>
        </div>

        <img
          class="auth-footer-mask flip-in-rtl"
          :src="authThemeMask"
          alt="auth-footer-mask"
          height="280"
          width="100"
        >
      </div>
    </VCol>

    <VCol
      cols="12"
      md="4"
      class="auth-card-v2 d-flex align-center justify-center"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-6"
      >
        <VCardText>
          <div class="text-xl mb-1 text-secondary">
            ស្វាគមន៏មកកាន់ <span class="font-bold text-secondary">{{ themeConfig.app.title }}</span>
          </div>
          <p class="mb-0 text-secondary">
            សូមផ្តល់ព័ត៌មានសំងាត់ដើម្បីចូលទៅកាន់ប្រព័ន្ធ
          </p>
        </VCardText>
        <VCardText>
          <VForm @submit.prevent="login">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.email"
                  autofocus
                  label="ឈ្មោះគណនី រឺ អុីម៉ែល"
                  type="email"
                  placeholder="សូមបញ្ចូលឈ្មោះគណនី រឺ អុីម៉ែល"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  label="អក្សរសម្ងាត់"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="password"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center flex-wrap justify-space-between my-6">
                  <VCheckbox
                    v-model="form.remember"
                    label="ចងចាំ"
                  />
                  <a
                    class="text-primary"
                    href="javascript:void(0)"
                  >
                    ភ្លេចអក្សរសម្ងាត់?
                  </a>
                </div>

                <VBtn
                  block
                  type="submit"
                  :loading="isSubmitting"
                  :disabled="isSubmitting"
                >
                  ចូលទៅកាន់ប្រព័ន្ធ
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
