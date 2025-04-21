<script lang="ts" setup>

const prop = defineProps({
    question_id: {
        type: Number,
        required: true
    },
    toggleDialog: {
        type: Function,
        required: true,
    },
})

const query = {
    by_question_id: prop.question_id,
    limit: 10,
}

const { data: evaluationData, execute } = await useApi<any>(createUrl('/v1/evaluation-criterias', { query }));

let evaluationForm = reactive<{ [key: number]: any }>({});
      
async function initForm(){
    console.log('object',evaluationData.value);
    evaluationData.value.data.forEach((quest: any) => {
        const options: any[] = JSON.parse(quest.options)

console.log('running'); 
        evaluationForm[quest.id] = {
            title: quest.title,
            question_id: quest.question_id,
            options: options,
            
            id: quest.id,
            is_modified: false,
        };
    })
}

function addEval(){
    const key = generateKey();
    evaluationForm[key] = {
        title: '',
        question_id: prop.question_id,
        options: [
            {
                title: '',
                point: 0
            }
        ],

        id: key,
        is_modified: false,
    };
}

function addEvalOption(key: number | string){
    evaluationForm[key].options.push({ title: '', point: 0 });
}

function setModified(key: number | string ){
    // since  if its a type of key that mean it is generated and therefore a new eval just added so its not modifed
    if(typeof key == 'string') return;

    evaluationForm[key].is_modified = true
}
async function submit(){
    const promises = Object.entries(evaluationForm).map(async ([key, question]: any) => {

        //this condition check if the key is generated which mean it is new there more we will create instead of update. 
        // generated key are string of random letter therefore it will be NaN if try to parse it
        if(isNaN(Number(key)) ){
            await $api('/v1/evaluation-criterias', {
                method: 'POST',
                body: {
                    question_id: question?.question_id,
                    options: question.options,
                    title: question?.title
                }
            })
            return;
        }

        if(question.is_modified){
            await $api('/v1/evaluation-criterias/' + key, {
                method: 'POST',
                body: {
                    _method: 'patch',
                    question_id: question?.question_id,
                    options: question.options,
                    title: question?.title
                }
            })
        }
    })

    await Promise.all(promises);
    prop.toggleDialog();
}

initForm();

</script>

<template>
    <VCard class="text-secondary">
        <VCol>
            <VCardTitle>
                <span class="text-lg text-secondary">កម្រិតផ្ទៀងផ្ទាត់សូចនករ</span>
            </VCardTitle>
            <VCardText class="flex flex-col">
                <div class="flex border" v-for="([key, eva], index) in Object.entries(evaluationForm)" :key="eva.id">
                    <VTextarea @input="setModified(eva.id)" variant="outlined" class="px-3 py-3 w-2/3 max-w-2/3" auto-grow v-model="eva.title" placeholder="សូមបបំញ្ចូលការវាយតម្លែ"/>
                    <div class="px-2 w-1/3 border-l border-l-gray-200">
                        <!-- answer option -->
                        <div class="flex gap-1 items-center justify-between" v-for="ans in eva.options">
                            <div class="flex items-center flex-1 gap-1">
                                <VCheckbox :disabled="true"/>
                                <VTextField @input="setModified(eva.id)" class="w-full" v-model="ans.title" density="compact" variant="underlined"/>
                            </div>

                            <div class="flex gap-2 items-center py-2">
                                <span class="">ពិន្ទុ</span>
                                <AppTextField @input="setModified(eva.id)" v-model="ans.point" maxlength="3" class="w-[6ch]" density="compact"/>
                            </div>
                        </div>
                        <VBtn size="small" variant="plain" prepend-icon="tabler-plus" @click="addEvalOption(eva.id)">បន្ថែម</VBtn>
                    </div>
                </div>

                <VCol class="flex justify-end">
                    <VBtn size="small" variant="plain" prepend-icon="tabler-plus" @click="addEval()">បន្ថែម</VBtn>
                </VCol>
                <!-- when there is no evalutation data -->
                <div v-if="Object.entries(evaluationForm).length == 0" class="flex justify-center text-lg opacity-70">
                    <span>គ្មានការផ្ទៀងផ្ទាត់សូចនករ</span>
                </div>
            </VCardText>

            <VCardText class="d-flex justify-end flex-wrap gap-3 mt-3">
                <VBtn variant="tonal" color="secondary" @click="prop.toggleDialog">
                    បោះបង់ចោល
                </VBtn>
                <VBtn color="primary" v-if="Object.entries(evaluationForm).length > 0" @click="submit()">
                    រក្សាទុក
                </VBtn>
            </VCardText>
        </VCol>
    </VCard>
</template>
