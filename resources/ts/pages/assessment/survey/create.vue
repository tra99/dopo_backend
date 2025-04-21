<script lang="ts" setup>
import { useSnackbarStore } from '@/@core/stores/snackbar';
import { router } from '@/plugins/1.router';

const snackbar = useSnackbarStore();

const form = ref({
    title: '',
    start_date: null,
    end_date: null,
    school_type: null,
    is_published: false,
    is_evaluated: false,
    description: '',
})

async function submit(){
  await $api('/v1/surveys' , {
    method: 'POST',
    body: form.value
  }).then((res)=>{
    router.replace('/assessment/survey')
  }
  ).catch(err => snackbar.openSnackbar(err?.response?._data?.error,'error'))
  

}
const school_types = [
  { title: 'Lower Secondary School', value: 'Lower Secondary School' },
  { title: 'Upper Secondary (7-12)', value: 'Upper Secondary (7-12)' },
  { title: 'Upper Secondary (10-12)', value: 'Upper Secondary (10-12)' },
]
</script>

<template>
  <section>
    <div class="d-flex flex-wrap justify-start justify-sm-space-between gap-y-4 gap-x-6 mb-6">
      <div class="d-flex flex-column justify-center">
        <h4 class="text-h3 font-weight-medium text-secondary">
          បង្កើតសម្រង់ព័ត៌មាន
        </h4>
        <div class="text-lg text-secondary">
          ជ្រើសរើសសំណួរដែលអ្នកចង់ប្រមូលព័ត៌មាន
        </div>
      </div>

      <div class="d-flex gap-4 align-center flex-wrap scale-95">
        <VBtn size="large" variant="tonal" color="secondary" to="/assessment/survey">
          បោះបង់ចោល
        </VBtn>
        <VBtn size="large" variant="tonal" color="primary" @click="submit">
          រក្សាទុកសិន
        </VBtn>
        
        <!-- TODO: make it so that when click this button it will change is_published to be true -->
        <VBtn size="large" @click="form.is_published = true; submit();">ដាក់អោយដំណើរការ</VBtn>
      </div>
    </div>
  </section>


  <!-- form -->
  <VCard>
    <VCol>
        <VCardTitle>
            <h3 class="text-secondary">ព័ត៌មានទូទៅ</h3>
        </VCardTitle>
        <VCol>
            <VForm>
                <VRow>
                    <VCol cols="6">
                        <AppTextField
                        v-model="form.title"
                        label="ឈ្មោះ"
                        placeholder="ឈ្មោះ"
                        />
                    </VCol>
                    <VCol cols="6">
                        <AppSelect
                        v-model="form.school_type"
                        :items="school_types"
                        item-title="title"
                        item-value="value"
                        label="ប្រភេទសាលា​"
                        placeholder="ប្រភេទសាលា"
                        />
                    </VCol>
                    <VCol cols="6">
                        <AppDateTimePicker
                            prepend-inner-icon="tabler-calendar-plus"
                            v-model="form.start_date"
                            label="ចាប់ពីថ្ញៃទី"
                            placeholder="មិនបញ្ចាក់"
                        />
                    </VCol>
                    <VCol cols="6">
                        <AppDateTimePicker
                            prepend-inner-icon="tabler-calendar-plus"
                            v-model="form.end_date"
                            label="ដល់ថ្ញៃទី"
                            placeholder="មិនបញ្ចាក់"
                        />
                    </VCol>
                    <VCol cols="12">
                        <VCheckbox v-model="form.is_evaluated" label="សម្រាប់សាលារៀនធ្វើការវាយតម្លៃដោយខ្លួនឯង"/>
                    </VCol>
                    <VCol cols="12" class="opacity-80">
                        <AppTextarea
                        v-model="form.description"
                        label="សេចក្តីសំរាយ"
                        placeholder="ជាលក្ខណៈសំគាល់បន្ថែម"/>
                    </VCol>
                </VRow>
            </VForm>
        </VCol>
    </VCol>
  </VCard>
</template>
