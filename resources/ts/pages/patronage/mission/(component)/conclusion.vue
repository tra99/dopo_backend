<script lang="ts" setup>
import { IMission, School } from '../interface';


defineExpose({ submitForm });
const prop = defineProps({
    is_edit: {
        type: Boolean,
        required: true
    },
    missions: {
        type: Object as PropType<IMission | null>,
        required: true
    },
    schools: {
        type: Array as PropType<School[] | undefined>,
        required: true
    },
    hide_file: {
        type: Boolean,
        require: false,
    }
})

const conclusionForm = ref({
    _method: 'patch',
    conclusion: prop.missions?.conclusion,
    perspective: prop.missions?.perspective,
    appendix: prop.missions?.appendix,
});

const report_file = ref<File>();
const attendance_file = ref<File>();
const assessment_file = ref<File>();
const slide_file = ref<File>();

async function submitForm() {

    const body: FormData = new FormData();

    if(conclusionForm.value.conclusion !== prop.missions?.conclusion) body.append('conclusion', conclusionForm.value.conclusion as string);
    if(conclusionForm.value.perspective !== prop.missions?.perspective) body.append('perspective', conclusionForm.value.perspective as string)
    if(conclusionForm.value.appendix !== prop.missions?.appendix) body.append('appendix', conclusionForm.value.appendix as string)

    if(report_file.value) body.append('report_file', report_file.value);
    if(attendance_file.value) body.append('attendance_file', attendance_file.value);
    if(assessment_file.value) body.append('assessment_file', assessment_file.value);
    if(slide_file.value) body.append('slide_file', slide_file.value);

    if(!body.entries().next().done) {
        await $api(`/v1/missions/${prop.missions?.id}/school`, {
            method: 'POST', 
            body: body,
        })
    }
}

</script>

<template>
  <VCol cols="12">
    <h1 class="text-lg text-secondary font-semibold mb-2">ការឆ្លុះបញ្ចាំងរបស់ក្រុមការងារ</h1>
    <VTextarea v-if="prop.is_edit || conclusionForm.perspective" auto-grow :readonly="!prop.is_edit" :variant="prop.is_edit?'outlined':'plain'" v-model="conclusionForm.perspective" placeholder="បញ្ចូលព័ត៌មាននៅទីនេះ"/>
  </VCol>

  <VCol cols="12">
    <h1 class="text-lg text-secondary font-semibold mb-2">សេចក្តីសន្និដ្ឋាន</h1>
    <VTextarea v-if="prop.is_edit || conclusionForm.conclusion" auto-grow :readonly="!prop.is_edit" :variant="prop.is_edit?'outlined':'plain'" v-model="conclusionForm.conclusion" placeholder="បញ្ចូលព័ត៌មាននៅទីនេះ"/>
  </VCol>

  <VCol cols="12">
    <h1 class="text-lg text-secondary font-semibold mb-2">ឧបសម្ព័ន្ធ (បទបង្ហាញ លទ្ធផលពិភាក្សាផ្អែកតាមសកម្មភាព)</h1>
    <VTextarea v-if="prop.is_edit || conclusionForm.appendix" auto-grow :readonly="!prop.is_edit" :variant="prop.is_edit?'outlined':'plain'" v-model="conclusionForm.appendix" placeholder="បញ្ចូលព័ត៌មាននៅទីនេះ"/>
  </VCol>

  <VCol cols="12" v-if="!prop.hide_file">
    <h1 class="text-lg text-secondary font-semibold mb-2">បន្ថែមឯកសារ</h1>
    <VCol class="flex flex-col gap-3">
        <div class="flex gap-6 items-center text-secondary">
            <span class="text-lg min-w-[16ch]"> <VIcon icon="tabler-file-upload"></VIcon> របាយការណ៍៖</span>
            <VChip v-if="report_file || prop.missions?.report_uri"><VIcon class="mr-1" icon="tabler-file-type-pdf"></VIcon> {{ report_file?.name || prop.missions?.report_uri?.split('/').pop() }}</VChip>
            <VFileInput v-if="prop.is_edit && !report_file && !prop.missions?.report_uri"
            v-model="report_file" 
            class="max-w-[18ch]" 
            :disabled="!prop.is_edit" 
            show-size
            label="ជ្រើសរើសឯកសារ" 
            variant="plain" 
            density="compact"
            prepend-icon="tabler-upload"/>
        </div>
        <div class="flex gap-6 items-center text-secondary">
            <span class="text-lg min-w-[16ch]"> <VIcon icon="tabler-file-upload"></VIcon> បញ្ចីវត្តមាន៖</span>
            <VChip v-if="attendance_file || prop.missions?.attendance_uri"><VIcon class="mr-1" icon="tabler-file-type-pdf"></VIcon> {{ attendance_file?.name || prop.missions?.attendance_uri?.split('/').pop() }}</VChip>
            <VFileInput v-if="prop.is_edit && !attendance_file && !prop.missions?.attendance_uri" 
            v-model="attendance_file" 
            class="max-w-[18ch]" 
            :disabled="!prop.is_edit" 
            show-size
            label="ជ្រើសរើសឯកសារ" 
            variant="plain" 
            density="compact"
            prepend-icon="tabler-upload"/>
        </div>
        <div class="flex gap-6 items-center text-secondary">
            <span class="text-lg min-w-[16ch]"> <VIcon icon="tabler-file-upload"></VIcon> បទបង្ហាញ៖</span>
            <VChip v-if="slide_file || prop.missions?.slide_uri"><VIcon class="mr-1" icon="tabler-file-type-pdf"></VIcon> {{ slide_file?.name || prop.missions?.slide_uri?.split('/').pop()  }}</VChip>
            <VFileInput v-if="prop.is_edit && !slide_file && !prop.missions?.slide_uri" 
                v-model="slide_file" 
                class="max-w-[18ch]"                                                                                                                      
                :disabled="!prop.is_edit" 
                show-size
                label="ជ្រើសរើសឯកសារ" variant="plain" 
                density="compact"
                prepend-icon="tabler-upload"/>
        </div>
        <div class="flex gap-6 items-center text-secondary">
            <span class="text-lg min-w-[16ch]"> <VIcon icon="tabler-file-upload"></VIcon> ឧបករណ៍ប្រើប្រាស់៖</span>
            <VChip v-if="assessment_file || prop.missions?.assessment_uri"><VIcon class="mr-1" icon="tabler-file-type-pdf"></VIcon> {{ assessment_file?.name || prop.missions?.assessment_uri?.split('/').pop()  }}</VChip>
            <VFileInput v-if="prop.is_edit && !assessment_file && !prop.missions?.assessment_uri"
            v-model="assessment_file"  
            class="max-w-[18ch]" 
            :disabled="!prop.is_edit" 
            show-size
            label="ជ្រើសរើសឯកសារ" 
            variant="plain" 
            density="compact"
            prepend-icon="tabler-upload"/>
        </div>
    </VCol>
  </VCol>
  
</template>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    