<script lang="ts" setup>
import { IAchievement, School } from '../interface';


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

const supportQuery = {
    is_parent: true,
}

const { data: achievementData } = await useApi<{ data: IAchievement[] }>(createUrl('/v1/achievements', { query })); 
const { data: supportData } = await useApi<{ data: any }>(createUrl('/v1/support-phases',{ query: supportQuery })); 
const stepData = computed((): any[] => 
    supportData.value?.data.map((item: any, index: number)=>({ title: `${convertToKhmerNumerals(index+1)}.${item.title}`, id: item.id }))
);

const achievementForm = ref<any[]>([]);

//option for select
const categoryOptions = ref<any[]>([]) 
const sochanakorOptions = ref<{}>({});

async function submitForm(){
    const bodyAchievement :any[] = []
    const bodyEvaluation :any[] = []

    achievementForm.value.forEach((school) => {
        console.log(school);
        school.forEach((step:any) => {
            bodyAchievement.push({
                ...step,
                achievements: step.achievements.flatMap((ach: any) => {
                    if (ach.support_phase_id) {
                        return [ach]
                    }
                    else if (ach.category_id) {
                        //while we still in map of achievemnt, might as well get the evaluation criteria  too
                        ach.sochanakors.forEach((soch:any) => {
                            soch.questions.forEach((quest: any) => {
                                quest.evaluations.forEach((eva: any) => {
                                    bodyEvaluation.push(eva);
                                });
                            });
                        });

                        const group_standard = ach.sochanakors.map((soch: any) => ({
                            category_id: soch.id
                        }))
                        return group_standard
                    }
                    return [];
                })
            })
        });    
    })

    bodyAchievement.forEach(async (ach: any) => {
        await $api('/v1/achievements', {
            method: 'POST',
            body: ach
        })
    })

    bodyEvaluation.forEach(async (eva: any) => {
       if(eva.is_modified){
            const evaBody = {
                ...eva,
                score: eva.options.find((option: any) => option.title === eva.result).point.toString()
            }
            await $api('/v1/evaluations', {
                method: 'POST',
                body: evaBody
            })
       }
    })
}

function initCategoryOptions(schools: School[]){
    schools.forEach(async (school,index) => {
        //TODO: add a function where if the school type is the same we can save some request from being sent to the api

        const catQuery = {
            school_type: school.school_type_en,
            type: 'standard'
        }
        const { data: categoryData } = await useApi<any>(createUrl('/v1/categories', { query: catQuery })); 
        categoryOptions.value[index] = [];

        categoryData.value?.data.forEach((item:any)=> {
            categoryOptions.value[index].push({
                title: item.title,
                id: item.id
            })
            sochanakorOptions.value[item.id] = item.children;
        });

        //TODO: each school can have different school type so check each school type and query the category option for both 
    })
    
}

// the hierarchy of the achievementform school:[step:[category: { sochanakor: [evaluation: []] }]]
// school [] -> step/standard []-> standard/step category {} -> sochanakor[] -> evaluation[]
function initForm(){
    achievementForm.value = [];
    prop.schools!.forEach((school, index) => {
        console.log('object',school);
        const achievement = achievementData.value?.data.filter(item => item.school_id === school.id);
        achievementForm.value[index] = [];
        
        achievement?.forEach((achieve,jndex) => {
            achievementForm.value[index].push({
                ...(achieve.id ? { id: achieve.id } : null),
                support_phase_id: achieve.support_phase_id,
                school_id: achieve.school_id,
                mission_id: achieve.mission_id,
                achievements: [] 
            })
            // this is the 2nd layer and for step it is the max layer for step form.
            Object.entries(achieve.categories_or_support_phases).forEach(([key, categories_or_support_phases]) => {
                achievementForm.value[index][jndex].achievements.push({
                    support_phase_id: categories_or_support_phases.support_phase.id,
                    id: categories_or_support_phases.id,
                    description: categories_or_support_phases.description,
                })
            })

            Object.entries(achieve.grouped_categories).forEach(([key, grouped_category]) => {
                achievementForm.value[index][jndex].achievements.push({
                    category_id: grouped_category[0].parent_id,
                    sochanakors: grouped_category.map((sochankor,kndex) => ({
                        id: sochankor.id,

                        questions: sochankor.questions.map((quest)=>({
                            id: quest.id,
                            question: quest.question,
                            //TODO: add points calculation later

                            evaluations: quest.evaluation_criterias.map((eva)=>({
                                school_id: school.id,
                                mission_id: prop.mission_id,
                                evaluation_criteria_id: eva.id,
                                options: JSON.parse(eva.options),
                                result: eva?.evaluations[0]?.result || '',
                                score: eva?.evaluations[0]?.score || null,

                                // ...( eva?.evaluations[0]?.id ? { id: eva?.evaluations[0]?.id}: null ),
                                title: eva?.title,
                                description: eva?.evaluations[0]?.description || '',
                                documents: eva?.evaluations[0]?.documents || '',

                                is_modified: false,
                            }))
                        }))
                    })),
                });
            })
        })
    })
}

function setModified(school_index: number, step_index: number, standard_index: number, sochanakor_index: number,question_index: number, evaluation_index: number){
    achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors[sochanakor_index].questions[question_index].evaluations[evaluation_index].is_modified = true;
}

function addStep(school_index: number){
    achievementForm.value[school_index].push({
        support_phase_id: null,
        school_id: prop.schools![school_index].id,
        mission_id: prop.mission_id,
        achievements: [] 
    })
}

function addStandard(school_index: number, step_index: number, type: string){
    if(type=='step'){
        achievementForm.value[school_index][step_index].achievements.push({
            support_phase_id: null
        })
    }
    else if(type=='standard'){
        achievementForm.value[school_index][step_index].achievements.push({
            category_id: 1,
            sochanakors: []
        })
    }
}
//if i dont add another function to handle the change it will cause infinite loop and crash the website
const onSochChange = (val, index, jndex, kndex, lndex) => {
    updateQuestion(index, jndex, kndex, lndex,val);
};

const updateQuestion = async (school_index:number, step_index:number, standard_index: number, sochanakor_index:number,val: number) => {
    const sochanakor_id = achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors[sochanakor_index].id
    achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors[sochanakor_index] = {
        id: sochanakor_id,
        questions: []
    }

    const query = {
        by_school_type: prop.schools![school_index].school_type_en,
        by_category_id: val,
    }

    const { data: questionData } = await useApi<any>(createUrl('/v1/questions', { query }));
    achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors[sochanakor_index] = {
        id: sochanakor_id,

        questions: questionData.value.data.map((quest: any)=>({
            question: quest.question,
            id: quest.id,
            evaluations: quest.evaluation_criterias.map((eva: any)=>({
                school_id: prop.schools![school_index].id,
                mission_id: prop.mission_id,
                evaluation_criteria_id: eva.id,
                options: JSON.parse(eva.options),
                result: '',
                score: '',
                title: eva?.title,
                description: '',
                documents: '',

                is_modified: false,
            }))
        }))
    }
}

async function addSochanakor(school_index:number, step_index:number, standard_index: number){
    const soch_index = achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors.length;
    achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors[soch_index] = {
        id: null,
        questions: []
    }

    const query = {
        school_type: prop.schools![school_index].school_type_en,
        by_category_id: achievementForm.value[school_index][step_index].achievements[standard_index].category_id,
    }

    const { data: questionData } = await useApi<any>(createUrl('/v1/questions', { query })); 
        achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors[soch_index] = {
        id: null,
        questions: questionData.value.data.map((quest: any)=>({
            question: quest.question,
            id: quest.id,
            evaluations: quest.evaluation_criterias.map((eva: any)=>({
                school_id: prop.schools![school_index].id,
                mission_id: prop.mission_id,
                evaluation_criteria_id: eva.id,
                options: JSON.parse(eva.options),
                result: eva.evaluations[0]?.result || '',
                score: eva.evaluations[0]?.score || '',
                title: eva.title,
                description: eva.evaluations[0]?.description || '',
                documents: eva.evaluations[0]?.documents || '',
            }))
        }))
    }
}

function removeSochanakor(school_index:number, step_index:number, standard_index: number, sochanakor_index: number){
    achievementForm.value[school_index][step_index].achievements[standard_index].sochanakors.splice(sochanakor_index, 1);
}
function removeStandard(school_index:number, step_index:number, standard_index: number){
    achievementForm.value[school_index][step_index].achievements.splice(standard_index, 1);
}

watch(() => prop.schools, (newSchools, oldSchools) => {
    if (newSchools) {
        initForm();
        initCategoryOptions(newSchools);
    }
}, { deep: true, immediate: true }); 

</script>

<template>
  <VCol cols="12">
    <h1 class="text-lg text-secondary font-semibold">ដំណើរការ និងលទ្ធផលសម្រេចបាន (ផ្នែកតាមផែនការសកម្មភាពចុះគាំទ្រ)</h1>

    <div class="border rounded-sm text-secondary mt-4 text-center">
        <!-- table header -->
        <div class="grid grid-cols-[6ch_2.5fr_1fr_2fr]">
            <span class="px-4 py-4 border text-nowrap">ល.រ</span>
            <span class="px-4 py-4 border text-nowrap">ស្តង់ដា / សូចនករ</span>
            <span class="px-4 py-4 border text-nowrap">លទ្ធផលសម្រេចបាន</span>
            <span class="px-4 py-4 border text-nowrap">បរិយាយ</span>
        </div>

        <!-- DATA table -->
        <template v-for="(school,index) in prop.schools">
            <span class="px-4 py-4 flex items-center border bg-gray-500/10" v-if="prop.schools!.length > 1">{{ school.school_name_kh }}</span>
            <!-- layer 1  step group -->
            <div class="grid grid-cols-[6ch_2.5fr_1fr_2fr]" v-for="(ach,jndex) in achievementForm[index]">
                <span class="py-4 border-r border-r-gray-300 text-nowrap">{{ convertToKhmerNumerals(jndex+1) }}</span>
                <div class="col-span-3">
                    <div class="flex items-center px-3 gap-2 my-2 justify-between">
                        <AppSelect 
                            :items="stepData || []"
                            :readonly="!prop.is_edit"
                            :variant="prop.is_edit?'outlined':'plain'"
                            v-model="ach.support_phase_id"
                            item-title="title"
                            item-value="id"
                            noDataText="គ្មានទិន្នន័យ"/>
                        <!-- <span> ពិន្ទុទទួលបាន៖ <span class="text-rose-500 dark:text-rose-400">{{ convertToKhmerNumerals(3) }}</span> </span> -->
                    </div>

                    <!-- layer 2: standard or step -->
                    <div class="grid grid-cols-[6ch_1fr]" v-for="(std,kndex) in ach.achievements">
                        <span class="py-4 border-r border-r-gray-300/80 dark:border-r-gray-600 text-nowrap">{{ convertToKhmerNumerals(`${jndex+1}.${kndex+1}`) }}</span>
                        <div class="mt-2">
                            <!-- if its standard grouping -->
                            <template v-if="std.category_id">
                                <div class="flex items-center px-3 gap-2 justify-between">
                                    <AppSelect 
                                        :items="categoryOptions[index]"
                                        item-title="title"
                                        item-value="id"
                                        v-model="std.category_id"
                                        :readonly="!prop.is_edit"
                                        :variant="prop.is_edit?'outlined':'plain'"
                                        noDataText="គ្មានទិន្នន័យ"/>
                                    <!-- <span> ពិន្ទុទទួលបាន៖ <span class="text-rose-500 dark:text-rose-400">{{ convertToKhmerNumerals(3) }}</span> </span> -->
                                </div>
                                <!-- layer 3: sochanakor -->
                                <div class="grid grid-cols-[6ch_1fr]" v-for="(soch,lndex) in std.sochanakors">
                                    <span class="py-4 border-r border-r-gray-300/80 dark:border-r-gray-600 text-nowrap text-sm tracking-tight">{{ convertToKhmerNumerals(`${jndex+1}.${kndex+1}.${lndex+1}`) }}</span>
                                    <div class="mt-2">
                                        <div class="flex items-center px-3 gap-2 justify-between">
                                            <AppSelect 
                                                :items="sochanakorOptions[std.category_id] || []"
                                                item-title="title"
                                                item-value="id"
                                                v-model="soch.id"
                                                :readonly="!prop.is_edit"
                                                :variant="prop.is_edit?'outlined':'plain'"
                                                noDataText="គ្មានទិន្នន័យ"
                                                @update:modelValue="val => onSochChange(val, index, jndex, kndex, lndex)"
                                            />
                                                <VBtn v-if="prop.is_edit" @click="removeSochanakor(index,jndex,kndex,lndex)" icon="tabler-x" variant="text" size="small"></VBtn>
                                            <!-- <span> ពិន្ទុទទួលបាន៖ <span class="text-rose-500 dark:text-rose-400">{{ convertToKhmerNumerals(3) }}</span> </span> -->
                                        </div>
                                        

                                        <!-- layer 4: question -->
                                        <div class="grid grid-cols-[6ch_1fr]" v-for="(quest,mndex) in soch.questions">
                                            <span class="py-4 mt-2 border-r border-r-gray-300/80 dark:border-r-gray-600 text-nowrap text-xs tracking-tight">{{ convertToKhmerNumerals(`${jndex+1}.${kndex+1}.${lndex+1}.${mndex+1}`) }}</span>

                                            <div class="mt-2">
                                                <div class="flex items-center px-3 gap-2 justify-between">
                                                    <span class="my-3 text-h6">{{ quest?.question }}</span>
                                                    <!-- <span> ពិន្ទុទទួលបាន៖ <span class="text-rose-500 dark:text-rose-400">{{ convertToKhmerNumerals(3) }}</span> </span> -->
                                                </div>
                                                
                                                <!-- layer 4: evaulation -->
                                                <div v-for="(eva,ndex) in quest.evaluations" class="grid grid-cols-[2.5fr_1fr_2fr] px-3 border-l border-l-transparent border-t border-b">
                                                    <!-- title -->
                                                    <VTextarea
                                                    class="my-2"
                                                    v-model="eva.title"
                                                    :readonly="!prop.is_edit"
                                                    :variant="prop.is_edit?'outlined':'plain'"
                                                    auto-grow rows="3"/>
                                                    
                                                    <!-- checkbox  -->
                                                    <div class="flex flex-col mx-2 py-2 border-l border-l-gray-200">
                                                        <VCheckbox 
                                                        v-for="box in eva.options"
                                                        :label="box.title"
                                                        v-model="eva.result"
                                                        :value="box.title"
                                                        @input="setModified(index,jndex,kndex,lndex,mndex,ndex)"
                                                        />
                                                    </div>

                                                    <!-- description  -->
                                                    <div class="flex flex-col pl-3 py-2 border-l border-l-gray-200">
                                                        <VTextarea
                                                            v-model="eva.description"
                                                            :readonly="!prop.is_edit"
                                                            :variant="prop.is_edit?'outlined':'plain'"
                                                            auto-grow rows="3"/>
                                                        <!-- <VChip v-if="eva.documents"><VIcon class="mr-1" icon="tabler-file-type-pdf"></VIcon> {{ eva.documents }}</VChip>
                                                        <VFileInput v-if="!eva.documents && prop.is_edit"

                                                            class="max-w-[18ch]" 
                                                            :readonly="!prop.is_edit"
                                                            label="បន្ធែមឯកសារ" 
                                                            variant="plain" 
                                                            density="compact"
                                                            prepend-icon="tabler-upload"/> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <VCol cols="1" v-if="prop.is_edit">
                                    <VBtn @click="addSochanakor(index,jndex,kndex)" size="small" variant="elevated" color="primary" prepend-icon="tabler-plus">បន្ថែម</VBtn>
                                </VCol>
                            </template>

                            <!-- if its step grouping -->
                            <template v-if="!std.category_id">
                                <div class="flex items-center px-3 gap-2 justify-between">
                                    <AppSelect 
                                        :items="supportData?.data.find(s => s.id == ach.support_phase_id).children || []"
                                        v-model="std.support_phase_id"
                                        item-title="title"
                                        item-value="id"
                                        :readonly="!prop.is_edit"
                                        :variant="prop.is_edit?'outlined':'plain'"
                                        noDataText="គ្មានទិន្នន័យ"/>
                                    <VBtn v-if="prop.is_edit" @click="removeStandard(index,jndex,kndex)" icon="tabler-x" variant="text" size="small"></VBtn>
                                    <!-- <span> ពិន្ទុទទួលបាន៖ <span class="text-rose-500 dark:text-rose-400">{{ convertToKhmerNumerals(3) }}</span> </span> -->
                                </div>
                                <div class="py-2 px-3 mx-3 my-2 border rounded-sm">
                                    <VTextarea 
                                    v-model="std.description"
                                    :readonly="!prop.is_edit"
                                    :variant="prop.is_edit?'outlined':'plain'" 
                                    rows="2"/>
                                </div>
                            </template>
                        </div>
                    </div>
                    <VCol cols="1" v-if="prop.is_edit">
                        <VMenu location="end">
                            <template #activator="{ props }">
                                <VBtn v-bind="props" size="small" variant="elevated" color="primary" prepend-icon="tabler-plus">ជ្រើសរើស</VBtn>
                            </template>
                            <VList>
                                <VListItem @click="addStandard(index,jndex,'standard')" >
                                    ស្តង់ដា
                                </VListItem>
                                <VListItem @click="addStandard(index,jndex,'step')" >
                                    ដំណាក់កាល
                                </VListItem>
                            </VList>
                        </VMenu>
                    </VCol>
                </div>
            </div>
            <VCol cols="1" v-if="prop.is_edit">
                <VBtn @click="addStep(index)" size="small" variant="elevated" color="primary" prepend-icon="tabler-plus">បន្ថែម</VBtn>
            </VCol>
        </template>
    </div>
  </VCol>
</template>
