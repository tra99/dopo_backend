<script lang="ts" setup>
import { useSnackbarStore } from '@/@core/stores/snackbar';
import { router } from '@/plugins/1.router';


const snackbar = useSnackbarStore();

const form = ref({
    school_ids: [],
    participant_ids: [],
    purpose: '',
    description: '', 
    start_date: '',
    end_date: '',
})

async function handleSubmit(){
  const res = await $api('/v1/missions', {
    method: 'POST',
    body: form.value
  })

  snackbar.openSnackbar('បង្កើតបេសកកម្មជោគជ័យ', 'success')
  router.replace('/patronage/mission')
}

// related to autocomplete school input
const school_search = ref<string[]>([])
const schools = ref<any[]>([])
const provinces = ref<string[]>([])
let timeout: ReturnType<typeof setTimeout> | null = null
const querySelectionSchool = (query: any, index: number) => {
    if (timeout) clearTimeout(timeout) // Clear previous timeout to debounce

    // Simulated ajax query
    timeout = setTimeout(async () => {

        try {
        const { data: schoolData } = await useApi<any>(
            createUrl(`/v1/schools?page=1&limit=25&sort_direction=asc&search=${school_search.value[index]}`)
        )

            schools.value[index] = schoolData.value?.data || []
        } catch (error) {
            console.error("Error fetching schools:", error)
        }
    }, 500)
}

function addSchool(){
    schools.value.push([])
    school_search.value.push('')
    provinces.value.push('')
    querySelectionSchool(' ', schools.value.length - 1);

}
function handleSelectSchool(province_name: string,index: number){
    provinces.value[index] = province_name
}



// related to autocomplete participant input
const participant_search = ref<string[]>([])
const participants = ref<any[]>([])
const position = ref<string[]>([])

const querySelectionparticipant = (query: any, index: number) => {
    if (timeout) clearTimeout(timeout) // Clear previous timeout to debounce

    // Simulated ajax query
    timeout = setTimeout(async () => {

        try {
        const { data: participantData } = await useApi<any>(
            createUrl(`/v1/participants?page=1&limit=25&sort_by=name&sort_direction=asc&search=${participant_search.value[index]}`)
        )

            participants.value[index] = participantData.value?.data || []
        } catch (error) {
            console.error("Error fetching participants:", error)
        } 
    }, 500)
}

function addParticipant(){
    participants.value.push([])
    participant_search.value.push('')
    position.value.push('')
    querySelectionparticipant(' ', participants.value.length - 1);
}
function handleSelectParticipant(position_name: string,index: number){
    position.value[index] = position_name || 'គ្មានតួនាទី'
}

const roles = [
    { title: 'អ្នកដឹកនាំបេសកកម្ម', value: 'team leader' },
    { title: 'អ្នកសរសេររបាយការណ៍', value: 'reporter' },
]

addSchool();

timeout = setTimeout(async () => {
    addParticipant()
}, 5)

</script>

<template>
    <VCard>
        <VCol>
            <VCardTitle>
                <h3 class="text-secondary">ព័ត៌មានបេសកកម្មថ្មី</h3>
            </VCardTitle>
            <VCol>
                <VForm @submit.prevent.left="() => { handleSubmit() }">
                    <VRow>
                        <!-- 👉 សាលា -->
                        <VCol cols="12">
                            <VRow class="items-start" v-for="(schol, index) in schools">
                                <VCol cols="6">
                                    <AppAutocomplete
                                        v-model:search="school_search[index]"
                                        v-model="form.school_ids[index]"
                                        label="ចុះទៅសាលារៀន"
                                        :items="schol"
                                        :item-title="(school: any) => `${school?.school_name_kh} (${school?.school_name_en})`"
                                        item-value="id"
                                        placeholder="ជ្រើសរើសសាសលារៀន"
                                        :menu-props="{ maxHeight: '200px' }"
                                        clearable
                                        @input="querySelectionSchool($event, index)"
                                    >
                                        <template #item="{ props, item }">
                                            <VListItem
                                                @click="handleSelectSchool(item?.raw?.province_kh,index)"
                                                v-bind="props"
                                                :title="item?.raw?.school_name_kh"
                                                :subtitle="item.raw?.school_name_en"
                                            />
                                        </template>
                                    </AppAutocomplete>
                                </VCol>
                                <!-- 👉 ខេត្ត -->
                                <VCol cols="6" class="flex gap-2 items-end">
                                    <AppTextField
                                        disabled
                                        readonly
                                        v-model="provinces[index]"
                                        label="ខេត្ត"
                                        placeholder="ខេត្ត"
                                    />
                                    <!-- Dialog Activator -->
                                    <VBtn v-if="index == 0"
                                        @click="addSchool"
                                        icon="tabler-plus"
                                        variant="tonal"
                                        color="primary"
                                        rounded
                                    />
            
                                </VCol>
                            </VRow>
                        </VCol>
                        <VCol cols="6">
                            <AppDateTimePicker
                                v-model="form.start_date"
                                label="ចាប់ពីថ្ញៃទី"
                                placeholder="ថ្ញៃ/ខែ/ឆ្នាំ"
                            />
                        </VCol>
                        <VCol cols="6">
                            <AppDateTimePicker
                                v-model="form.end_date"
                                label="ដល់ថ្ញៃទី"
                                placeholder="ថ្ញៃ/ខែ/ឆ្នាំ"
                            />
                        </VCol>
                        <!-- participant -->
                        <VCol cols="12">
                            <VRow class="items-base" v-for="(part, index) in participants">
                                <VCol cols="6">
                                    <AppAutocomplete
                                        v-model:search="participant_search[index]"
                                        v-model="form.participant_ids[index]"
                                        label="ក្រុមការងារ"
                                        :items="part"
                                        item-title="name"
                                        item-value="id"
                                        placeholder="ជ្រើសរើសក្រុមការងារ"
                                        :menu-props="{ maxHeight: '200px' }"
                                        clearable
                                        @input="querySelectionparticipant($event, index)"
                                    >
                                        <template #item="{ props, item }">
                                            <VListItem
                                                @click="handleSelectParticipant(item?.raw?.position,index)"
                                                v-bind="props"
                                                :title="item?.raw?.name"
                                                :subtitle="item.raw?.organization"
                                            />
                                        </template>
                                    </AppAutocomplete>
                                </VCol>
                                <!-- position -->
                                <VCol cols="6" class="flex gap-2 items-end">
                                    <AppSelect
                                        :items="roles"
                                        v-model="position[index]"
                                        label=""
                                        placeholder="ជ្រើសតួនាទី"
                                    />
                                    <!-- Dialog Activator -->
                                    <VBtn v-if="index == 0"
                                        @click="addParticipant"
                                        icon="tabler-plus"
                                        variant="tonal"
                                        color="primary"
                                        rounded
                                    />
            
                                </VCol>
                            </VRow>
                        </VCol>
                        <VCol cols="12">
                            <span class="text-secondary text-sm">គោលបំណងរបស់បេសកកម្ម</span>
                            <TiptapEditor
                                v-model="form.purpose"
                                placeholder="អ្នកអាចបញ្ចាក់គោលបំណង់នៅទីនេះបាន" 
                                class="border rounded basic-editor"
                            />
                        </VCol>
                        <VCol cols="12">
                            <span class="text-secondary text-sm">កំណត់ចំណាំ</span>
                            <TiptapEditor
                                v-model="form.description"
                                placeholder="សេចក្តីចំណាំផ្សេងៗ អាចដាក់នៅទីនេះបាន"
                                class="border rounded basic-editor"
                            />
                        </VCol>
                        <VCol cols="12" class="d-flex flex-row-reverse gap-4">
                            <VBtn type="submit">
                                រក្សាទុក
                            </VBtn>
                            <VBtn to="/patronage/mission" type="reset" color="secondary" variant="tonal">
                                បោះបង់ចោល
                            </VBtn>
                        </VCol>
                    </VRow>
                </VForm>
            </VCol>
        </VCol>
    </VCard>

</template>
