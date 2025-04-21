<script lang="ts" setup>
import { IMission } from '../interface';


const prop = defineProps({
    mission: {
        type: Object as PropType<IMission | null>,
        required: true
    }
})
const mission = computed((): IMission | null => prop.mission)

</script>

<template>
  <VCard variant="elevated">
    <!-- header -->
    <VCardTitle class="border-b">
        <VRow justify="space-between" class="items-center">
            <VCol cols="6" class="my-4">
              <div class="flex gap-3">
                <div class="bg-orange-200/20 w-16 h-16 flex justify-center items-center rounded-full ring-1 ring-secondary">
                  <VIcon color="secondary" size="52" icon="tabler-clipboard-data"/>
                </div>
                <div>
                  <h1 class="text-lg text-secondary font-semibold">ការចុះតាមដានសាលារៀន</h1>
                  <h1 class="text-lg text-secondary opacity-70">កាលបរិច្ឆេទ៖ {{ resolveDate([mission?.start_date!, mission?.end_date!]) }}</h1>
                </div>
              </div>
            </VCol>
            <VCol cols="6">
              <div class="flex gap-1 items-center justify-end">
                <h1 class="text-base text-secondary mr-2">កែប្រែចុងក្រោយ៖ {{ resolveDate(mission?.updated_at!) }}</h1>
                <VBtn icon="tabler-paperclip" variant="text" color="secondary"/> 
                <VBtn icon="tabler-star" variant="text" color="secondary"/> 
                <VBtn icon="tabler-edit" variant="text" color="secondary"/> 
              </div>
            </VCol>
        </VRow>
    </VCardTitle>

    <!-- detail grid -->
    <VCardText>
      <div class="border rounded-lg grid grid-cols-3 mt-5">
        <!-- mission location -->
        <div class="border-r border-r-gray-200 py-6 px-6">
            <div class="flex gap-3 text-secondary">
              <VIcon size="24" icon="tabler-map-pin" />
              <span class="text-lg opacity-70 font-semibold">ទីតាំង</span>
            </div>
            
            <div class="flex gap-6 my-6 mx-2 text-secondary">
              <span class="w-[7ch] text-end">សាលារៀន</span>
              <div class="flex flex-col gap-3 opacity-70">
                <span v-for="(sch,index) in mission?.schools!" :key="index">{{ sch.school_name_kh }}</span>
              </div>
            </div>

            <div class="flex gap-6 my-6 mx-2 text-secondary">
              <span class="w-[7ch] text-end">ខេត្ត</span>
              <div class="flex flex-col gap-3 opacity-70">
                <span v-for="(sch,index) in new Set(mission?.schools.map(s => s.province_kh))!" :key="index">{{ sch }}</span>
              </div>
            </div>
        </div>
        <!-- mission purpose -->
        <div class="border-r border-r-gray-200 py-6 px-6">
            <div class="flex gap-3 text-secondary">
              <VIcon size="24" icon="tabler-target" />
              <span class="text-lg opacity-70 font-semibold">គោលបំណង</span>
            </div>
            <div class="flex gap-6 my-6 mx-2 text-secondary">
              <div class="flex flex-col gap-3 opacity-70">
                <div v-html="mission?.purpose"></div>
              </div>
            </div>
        </div>
        <!-- mission participant -->
        <div class="border-r border-r-gray-200 py-6 px-6">
            <div class="flex gap-3 text-secondary">
              <VIcon size="24" icon="tabler-users" />
              <span class="text-lg opacity-70 font-semibold">ក្រុមការងារ</span>
            </div>

            <div class="flex gap-6 my-6 mx-2 text-secondary">
              <div class="flex flex-col gap-3 opacity-70">
                <span v-for="(miss,index) in mission?.participants" :key="index">{{ miss.name }}</span>
              </div>
            </div>
        </div>
      </div>
    </VCardText>

    <VCardText>
      <div class="text-secondary opacity-70" v-html="mission?.description"></div>
    </VCardText>
  </VCard>
</template>
