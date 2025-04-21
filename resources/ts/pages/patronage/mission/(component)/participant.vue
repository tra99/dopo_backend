<script lang="ts" setup>
import { useSnackbarStore } from '@/@core/stores/snackbar';
import { ISchoolParticipant, School } from '../interface';

const snackbar = useSnackbarStore();

//exposing the submitform function so that it can be pickup by the formRef at the parent component and execute the function
defineExpose({ submitForm });
const prop = defineProps({
    is_edit: {
        type: Boolean,
        required: true
    },
    schools: {
        type: Array as PropType<School[] | undefined>,
        required: true
    },
    mission_id: {
        type: Number,
        required: true
    }
})

const query = {
    by_mission_id: prop.mission_id, 
}
const { data: participantData } = await useApi<ISchoolParticipant[]>(createUrl('/v1/school-participants', { query }));

const participantForm = ref<any[]>([]);

async function submitForm(){

    const body: any[] = [];

    participantForm.value.map((participant)=>{
        Object.entries(participant).map(async ([key, value]: any) => {
            if(value?.is_modified){
               body.push(value);
            }
        })
    })

    if(body.length === 0) return;

    await $api('/v1/school-participants', {
        method: 'POST',
        body,
    }).then((res)=>{
        snackbar.openSnackbar(res?.message,'success')
    }).catch(err => snackbar.openSnackbar(err?.response?._data?.error,'error'))
}

function InitForm(){
    //clear the form empty to avoid old data since we are selecting school alot
    participantForm.value = [];

    prop.schools?.map((school)=>{
        const participant = participantData.value?.find(item => item.school.school_name_kh === school.school_name_kh);

        participantForm.value.push(
            {
                'នាយក/នាយិកា (រង)': {
                    number_of_total: participant?.data.find(item => item.organization === 'នាយក/នាយិកា (រង)')?.number_of_total || null,
                    number_of_female: participant?.data.find(item => item.organization === 'នាយក/នាយិកា (រង)')?.number_of_female || null,
                    school_id: school.id,
                    mission_id: prop.mission_id,
                    organization: 'នាយក/នាយិកា (រង)',
                    
                    is_modified: false,
                },
                'បុគ្គលិកមិនបង្រៀន': {
                    number_of_total: participant?.data.find(item => item.organization === 'បុគ្គលិកមិនបង្រៀន')?.number_of_total || null,
                    number_of_female: participant?.data.find(item => item.organization === 'បុគ្គលិកមិនបង្រៀន')?.number_of_female || null,
                    school_id: school.id,
                    mission_id: prop.mission_id,
                    organization: 'បុគ្គលិកមិនបង្រៀន',
                    
                    is_modified: false,
                },
                'បុគ្គលិកបង្រៀន': {
                    number_of_total: participant?.data.find(item => item.organization === 'បុគ្គលិកបង្រៀន')?.number_of_total || null,
                    number_of_female: participant?.data.find(item => item.organization === 'បុគ្គលិកបង្រៀន')?.number_of_female || null,
                    school_id: school.id,
                    mission_id: prop.mission_id,
                    organization: 'បុគ្គលិកបង្រៀន',
                    
                    is_modified: false,
                },
                'ក្រុមប្រិក្សា កុមា / សិស្ស': {
                    number_of_total:  participant?.data.find(item => item.organization === 'ក្រុមប្រិក្សា កុមា / សិស្ស')?.number_of_total || null,
                    number_of_female: participant?.data.find(item => item.organization === 'ក្រុមប្រិក្សា កុមា / សិស្ស')?.number_of_female || null,
                    school_id: school.id,
                    mission_id: prop.mission_id,
                    organization: 'ក្រុមប្រិក្សា កុមា / សិស្ស',
                    
                    is_modified: false,
                },
                'តំនាងសហគមន៍ / មាតាបិតា': {
                    number_of_total:  participant?.data.find(item => item.organization === 'តំនាងសហគមន៍ / មាតាបិតា')?.number_of_total || null,
                    number_of_female: participant?.data.find(item => item.organization === 'តំនាងសហគមន៍ / មាតាបិតា')?.number_of_female || null,
                    school_id: school.id,
                    mission_id: prop.mission_id,
                    organization: 'តំនាងសហគមន៍ / មាតាបិតា',
                    
                    is_modified: false,
                }
            },
        )
    })
}

function resolveTotalParticipant(index: number){
    let total = 0;
    Object.entries(participantForm.value[index]).map(([key, value]: any) => {
        total += +value.number_of_total;
    })
    return total;
}

function resolveTotalFemale(index: number){
    let total = 0;
    Object.entries(participantForm.value[index]).map(([key, value]: any) => {
        total += +value.number_of_female;
    })
    return total;
}

function setModified(key: string ,index: number){
    participantForm.value[index][key].is_modified = true;
}

watch(() => prop.schools, (newSchools, oldSchools) => {
    if (newSchools) {
        InitForm();
    }
}, { deep: true, immediate: true }); 


</script>

<template>
  <VCol cols="12">
    <h1 class="text-lg text-secondary font-semibold">សមាសភាពភាគីពាក់ព័ន្ធដែលទទួលបានការគាំទ្រ</h1>

    <div class="border rounded-sm text-secondary mt-4 text-center">
        <!-- table header -->
        <div class="grid" :class="prop.schools!.length > 1 ? 'grid-cols-7' : 'grid-cols-6'">
            <span class="px-4 py-4 flex items-center justify-center border" v-if="prop.schools!.length > 1">សាលារៀនគោលដៅ</span>
            <div class="grid grid-cols-2">
                <span class="col-span-2 px-4 py-4 border text-nowrap">នាយក/នាយិកា (រង)</span>
                <span class="px-4 py-4 border">សរុប</span>
                <span class="px-4 py-4 border">ស្រី</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="col-span-2 px-4 py-4 border text-nowrap">បុគ្គលិកមិនបង្រៀន</span>
                <span class="px-4 py-4 border">សរុប</span>
                <span class="px-4 py-4 border">ស្រី</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="col-span-2 px-4 py-4 border text-nowrap">បុគ្គលិកបង្រៀន</span>
                <span class="px-4 py-4 border">សរុប</span>
                <span class="px-4 py-4 border">ស្រី</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="col-span-2 px-4 py-4 border text-nowrap ">ក្រុមប្រិក្សា កុមា / សិស្ស</span>
                <span class="px-4 py-4 border">សរុប</span>
                <span class="px-4 py-4 border">ស្រី</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="col-span-2 px-4 py-4 border text-nowrap">តំនាងសហគមន៍ / មាតាបិតា</span>
                <span class="px-4 py-4 border">សរុប</span>
                <span class="px-4 py-4 border">ស្រី</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="col-span-2 px-4 py-4 border text-nowrap">សរុប</span>
                <span class="px-4 py-4 border">សរុប</span>
                <span class="px-4 py-4 border">ស្រី</span>
            </div>
        </div>

        <!-- DATA table -->
        <template v-for="(school,index) in prop.schools">
            <div class="grid" :class="prop.schools!.length > 1 ? 'grid-cols-7' : 'grid-cols-6'">
                <span class="px-4 py-4 flex items-center border" v-if="prop.schools!.length > 1">{{ school.school_name_kh }}</span>
                <!-- 1. នាយក/នាយិកា (រង) -->
                <div class="grid grid-cols-2">
                    <span class="border flex items-center px-3 py-1">
                        <VTextField @input="setModified('នាយក/នាយិកា (រង)',index)" v-model="participantForm[index]['នាយក/នាយិកា (រង)'].number_of_total" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5" />
                    </span>
                    <span class="border flex items-center px-3">
                        <VTextField @input="setModified('នាយក/នាយិកា (រង)',index)" v-model="participantForm[index]['នាយក/នាយិកា (រង)'].number_of_female" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5"/>
                    </span>
                </div>

                <!-- 2. បុគ្គលិកមិនបង្រៀន -->
                <div class="grid grid-cols-2">
                    <span class="border flex items-center px-3 py-1">
                        <VTextField @input="setModified('បុគ្គលិកមិនបង្រៀន',index)" v-model="participantForm[index]['បុគ្គលិកមិនបង្រៀន'].number_of_total" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5" />
                    </span>
                    <span class="border flex items-center px-3">
                        <VTextField @input="setModified('បុគ្គលិកមិនបង្រៀន',index)" v-model="participantForm[index]['បុគ្គលិកមិនបង្រៀន'].number_of_female" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5"/>
                    </span>
                </div>

                <!-- 3. បុគ្គលិកបង្រៀន -->
                <div class="grid grid-cols-2">
                    <span class="border flex items-center px-3 py-1">
                        <VTextField @input="setModified('បុគ្គលិកបង្រៀន',index)" v-model="participantForm[index]['បុគ្គលិកបង្រៀន'].number_of_total" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5" />
                    </span>
                    <span class="border flex items-center px-3">
                        <VTextField @input="setModified('បុគ្គលិកបង្រៀន',index)" v-model="participantForm[index]['បុគ្គលិកបង្រៀន'].number_of_female" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5"/>
                    </span>
                </div>

                <!-- 4. ក្រុមប្រិក្សា កុមា / សិស្ស -->
                <div class="grid grid-cols-2">
                    <span class="border flex items-center px-3 py-1">
                        <VTextField @input="setModified('ក្រុមប្រិក្សា កុមា / សិស្ស',index)" v-model="participantForm[index]['ក្រុមប្រិក្សា កុមា / សិស្ស'].number_of_total" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5" />
                    </span>
                    <span class="border flex items-center px-3">
                        <VTextField @input="setModified('ក្រុមប្រិក្សា កុមា / សិស្ស',index)" v-model="participantForm[index]['ក្រុមប្រិក្សា កុមា / សិស្ស'].number_of_female" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5"/>
                    </span>
                </div>

                <!-- 5. តំនាងសហគមន៍ / មាតាបិតា -->
                <div class="grid grid-cols-2">
                    <span class="border flex items-center px-3 py-1">
                        <VTextField @input="setModified('តំនាងសហគមន៍ / មាតាបិតា',index)" v-model="participantForm[index]['តំនាងសហគមន៍ / មាតាបិតា'].number_of_total" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5" />
                    </span>
                    <span class="border flex items-center px-3">
                        <VTextField @input="setModified('តំនាងសហគមន៍ / មាតាបិតា',index)" v-model="participantForm[index]['តំនាងសហគមន៍ / មាតាបិតា'].number_of_female" type="text" :readonly="!prop.is_edit" :variant="!prop.is_edit? 'plain':'underlined'" maxlength="5"/>
                    </span>
                </div>
                
                <div class="grid grid-cols-2">
                    <span class="border flex items-center px-3 py-1">
                        <span class="text-center">{{ resolveTotalParticipant(index) }}</span>
                    </span>
                    <span class="border flex items-center px-3">
                        <span class="text-center">{{ resolveTotalFemale(index) }}</span>
                    </span>
                </div>

            </div>
        </template>
    </div>
  </VCol>
</template>

<style scoped>
::v-deep(.v-input input) {
  text-align: center;
}
</style>
