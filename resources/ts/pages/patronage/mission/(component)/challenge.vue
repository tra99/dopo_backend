<script lang="ts" setup>
import { IChallenge, School } from '../interface';


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
    by_mission_id: prop.mission_id
}
const { data: challengeData } = await useApi<IChallenge[]>(createUrl('/v1/challenges', { query }));
const { data: categoryData } = await useApi<any>(createUrl(`/v1/categories?type=standard`));
const categories = computed((): any[] => categoryData.value?.data).value.map((category) => { return { title: category.title,  value: category.id }});

const challengeForm = ref<any[]>([]);

async function submitForm(){
    const newChallenges: any[] = [];
    const updateChallenges: any[] = [];

    challengeForm.value.map((school,index)=>{
        school.map((challenge: any, jndex: number)=>{
            if(challenge.is_new) newChallenges.push(challenge);
            if(challenge.is_modified) updateChallenges.push(challenge);
        })
    });

    if(newChallenges.length) {
        newChallenges.map(async (challenge: any) => {
            await $api('/v1/challenges', { 
                method: 'POST', 
                body: challenge 
            })
        })
    }
    if(updateChallenges.length)  {
        updateChallenges.map(async (challenge)=>{
            await $api(`/v1/challenges/${challenge.id}`, { 
                method: 'PATCH', 
                body: challenge 
            })
        })
    }
}

function InitForm(){
    challengeForm.value = [];
    prop.schools?.map((school,index)=>{
        const challenge = challengeData.value?.find(item => item.school.id === school.id);
        if(!challenge) return;

        console.log(index);
        challengeForm.value[index] = [];
        challenge?.data.map((category,jndex)=>{
            challengeForm.value[index][jndex] = {
                challenge: category.challenge || '',
                solution: category.solution || '',
                school_id: school.id,
                mission_id: prop.mission_id, 
                category_id: category.category_id || null,               

                id: category.id,
                is_modified: false,
                is_new: false,
            }
        })
    });
}

function addChallenge(schoolIndex: number){
    if(!challengeForm.value[schoolIndex])
        challengeForm.value[schoolIndex] = [];

    challengeForm.value[schoolIndex].push({
        challenge: '',
        solution: '',
        school_id: prop?.schools![schoolIndex].id,
        mission_id: prop.mission_id, 
        category_id: null,               

        is_modified: false,
        is_new: true,
    })
}

function setModified(schoolIndex: number, index: number){
    if(challengeForm.value[schoolIndex][index].is_new) return;
    challengeForm.value[schoolIndex][index].is_modified = true;
}

watch(() => prop.schools, (newSchools, oldSchools) => {
    if (newSchools) {
        InitForm();
    }
}, { deep: true, immediate: true }); 

</script>

<template>
    <VCol cols="12">
    <h1 class="text-lg text-secondary font-semibold">បញ្ហាប្រឈមតាមសាលារៀន​</h1>

    <div class="border rounded-sm text-secondary mt-4 text-center">
        <!-- table header -->
        <div class="grid grid-cols-[6ch_1fr_1fr_1fr]">
            <span class="px-4 py-4 border text-nowrap">ល.រ</span>
            <span class="px-4 py-4 border text-nowrap">ស្តង់ដា / សូចនករ</span>
            <span class="px-4 py-4 border text-nowrap">បញ្ហាប្រឈម</span>
            <span class="px-4 py-4 border text-nowrap">ដំណោះស្រាយ</span>
        </div>

        <!-- DATA table -->
        <template v-for="(school,index) in prop.schools">
            <span class="px-4 py-4 flex items-center border bg-gray-500/10" v-if="prop.schools!.length > 1">{{ school.school_name_kh }}</span>
            <div class="grid grid-cols-[6ch_1fr_1fr_1fr]" v-for="(chal,jndex) in challengeForm[index]">
                <span class="px-4 py-4 border text-nowrap">{{ convertToKhmerNumerals(jndex+1) }}</span>
                <div class="border flex px-2 py-2">
                    <AppSelect 
                        v-model="chal.category_id"
                        :items="categories"
                        :readonly="!prop.is_edit"
                        >
                        <template #item="{ item, props }">
                            <VListItem @click="setModified(index,jndex)" v-bind="props"/>
                        </template>
                    </AppSelect>
                </div>
                <div class="border flex items-center px-2 py-2">
                    <VTextarea @input="setModified(index,jndex)" v-model="chal.challenge" rows="3" density="compact" :readonly="!prop.is_edit" auto-grow :variant="prop.is_edit?'outlined':'plain'" placeholder="បញ្ចូលព័ត៌មាននៅទីនេះ"/>
                </div>
                <div class="border flex items-center px-2 py-2">
                    <VTextarea @input="setModified(index,jndex)" v-model="chal.solution" rows="3" density="compact" :readonly="!prop.is_edit" auto-grow :variant="prop.is_edit?'outlined':'plain'" placeholder="បញ្ចូលព័ត៌មាននៅទីនេះ"/>
                </div>
            </div>
            <VCol cols="1" v-if="prop.is_edit">
                <VBtn size="small" variant="elevated" color="primary" @click="addChallenge(index)" prepend-icon="tabler-plus">បន្ថែម</VBtn>
            </VCol>
            <VCol ccols="1" v-if="!prop.is_edit && !challengeForm[index]">
                <span class="text-center opacity-50 text-h6">មិនទាន់មានបញ្ហាប្រឈម</span>
            </VCol>
        </template>
    </div>
  </VCol>
</template>
