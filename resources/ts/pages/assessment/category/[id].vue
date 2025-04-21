<script lang="ts" setup>
import { router } from '@/plugins/1.router';
import { generateKey } from '@/utils/generate';
import QuestionDialog from './(component)/QuestionDialog.vue';
import { ICategory, IQuestion } from './interface';

// dividing the code into small component made things more complicated so i wrote it in one file to better manage state.
const isEditTitle = ref(false);
const isDialogVisible = ref(false);
const selected_question_id = ref<number>();


const titleForm = ref({
  _method: 'patch',
  title: '',
  school_type_en: '',
  school_type_kh: '',
})

/** 
 * * A object to dynamically create the form ref 
 * i make the key to be its own id
 * the value is the form requirement for each api endpoint
*/

const categoriesForm = shallowReactive<{ [key: number]: any }>({});
const sochanakorForm = shallowReactive<{ [key: number]: any[] }>({});
const questionsForm = shallowReactive<{ [key: number]: any[] }>({});
const answerForm = shallowReactive<{ [key: number]: any[] }>({});

/** @Form_Structure_Example
 * categoryForm { [category.id]: category }
 * sochanakorForm { [sochanakor.id]: sochanakor[] }
 * questionForm { [ category_id || sochanakor.id ]: question[] }
 * answerForm { [ question.id ]: answer[] }
 */

const Param: any = router.currentRoute.value.params;

const { data: categoryData, execute } = await useApi<any>(createUrl('/v1/category-groups/' + Param?.id));
// const { data: categoryData, execute } = await useApi<any>(createUrl('/v1/category-groups/3')); // for testing cuz its annoying to refresh the page since hot reload doesnt get paramid

const school_type_id = categoryData.value?.id

watchEffect(() => {
  if (categoryData.value) {
    titleForm.value.title = categoryData.value.title;
    titleForm.value.school_type_en = categoryData.value.school_type_en;
    titleForm.value.school_type_kh = categoryData.value.school_type_kh;
  }
});

const addAnswer = (answer?: { title: string, point: number },key?: string, question_type?: string) => {
  if(!answerForm[key!]){
    answerForm[key!] = reactive([]);
  }

  if(question_type == 'text'){
    const ans = answer?.title.split('-')

    // this condition is for when adding new empty answer
    if(!ans) {
      answerForm[key!].push(reactive({
        title: ['',''],
        point: 0,
      }))
      return
    };
    // adding answer from existing rule data
    answerForm[key!].push(reactive({
      title: [ans[0]?? '', ans[1]?? ''],
      point: 0,
    }))
    return;
  }

  answerForm[key!].push(reactive({
    title: answer?.title || '',
    point: answer?.point || 0,
  }))
}

function removeAnswer(key?: string, index?: number){
  answerForm[key!].splice(index!, 1);
  questionsForm[key!][index].is_modified = true;
}

// add one category ref to the category form to be tracked
const addCategory = (category?: ICategory, status?: { is_new: boolean, is_modified: boolean }) => {
  const categoryKey = category?.id || generateKey();
  
  categoriesForm[categoryKey] = reactive({
    type: 'standard',
    title: category?.title || '',
    parent_id: category?.parent_id || null,
    school_type: categoryData.value?.school_type_en,
    school_type_id,

    id: category?.id,
    is_new: status?.is_new,
    is_modified: status?.is_modified
  });

};

// Add a Sochanakor to the reactive form
const addSochanakor = (sochanakor?: ICategory, status?: { is_new: boolean, is_modified: boolean }, key?: string) => {
  const parentKey = key || sochanakor?.parent_id || generateKey();
  if (!sochanakorForm[parentKey]) {
    sochanakorForm[parentKey] = reactive([]);
  }
  sochanakorForm[parentKey!].push(reactive({
    type: 'sochanakor',
    title: sochanakor?.title || '',
    parent_id: sochanakor?.parent_id || key,
    school_type: categoryData.value?.school_type_en,
    school_type_id,

    //id isnt used so u can use it as a key
    id: sochanakor?.id || parentKey,
    is_new: status?.is_new,
    is_modified: status?.is_modified
  }));

  // key : question[]
};

// Add a Question to the reactive form
const addQuestion = (question?: IQuestion, status?: { is_new: boolean, is_modified: boolean }, key?: string) => {
  const categoryKey = key || question?.category_id;
  if (!questionsForm[categoryKey!]) {
    questionsForm[categoryKey!] = reactive([]);
  }
  questionsForm[categoryKey!].push(reactive({
    category_id: question?.category_id || categoryKey ||  null,
    question: question?.question || '',
    description: question?.description || '',
    question_type: question?.question_type || 'radio',
    school_type: titleForm.value.school_type_en,
    // published_at: null,
    // ...(question?.rule? { rule: question.rule }: null),
    // ...(question?.answer_option? { answer_option: question.answer_option }: null),

    id: question?.id || generateKey(),
    hasDescription: question?.description != '' ? true : false,
    is_new: status?.is_new,
    is_modified: status?.is_modified
  }));
};

// this is used when the input is updated so we set the form of that specific form to be in modified state then we can update only the change form with api and save some redundance request
const setCategoryModified = (key: string) => {
  if(!categoriesForm[key]) return;
  if(categoriesForm[key].is_new) return;
  return categoriesForm[key].is_modified = true; 
};
const setSochanakorModified = (key: string) => {
  if(!sochanakorForm[key]) return;
  if(sochanakorForm[key].is_new) return;
  return sochanakorForm[key].is_modified = true; 
};
const setQuestionModified = (key: string, index: number) => {
  if(!questionsForm[key]) return;
  if(questionsForm[key][index].is_new) return;
  questionsForm[key][index].is_modified = true;
};

// clear the input of answer because there is a thing where if we change the question type the old answer become readonly and unchangeable for some reason
const onQuestionTypeChange = (key: string) => {
  if(['text'].includes(key)){
    answerForm[key] = [];
  }
  if(['radio', 'checkbox'].includes(key)){
    // questionsForm[key][index] = [];
  }
}

const submit = () => {

  // submit and create categories
  Object.entries(categoriesForm).forEach(async ([key, category]) => {

    if(category.is_new){
      await $api('/v1/categories' , {
        method: 'POST',
        body: category
      })
      return;
    }
    
    if(category.is_modified){
      await $api(`/v1/categories/${category.id}` , {
        method: 'PATCH',
        body: category
      })
      return;
    }
  })

  //submit the sochanakor change or create new sochanakor
  Object.entries(sochanakorForm).forEach(async ([key, sochanakor]) => {
    sochanakor.forEach(async (sochanakor) => {

      if(sochanakor.is_new){
        await $api('/v1/categories' , {
          method: 'POST',
          body: sochanakor
        })
        return;
      }

      if(sochanakor.is_modified){
        await $api(`/v1/categories/${sochanakor.id}` , {
          method: 'PATCH',
          body: sochanakor
        })

        return;
      }
    })
  })



  Object.entries(questionsForm).forEach(async ([key, question]) => {
    const new_question_arr: any[] = []
    const modified_question_arr: any[] = []

    question.forEach(async (question) => {
      const body = {
        ...question,
        ...(['text'].includes(question.question_type)? 
        { rule: answerForm[question.id].map((item: any) => {
          return { point: item.point, title: `${item.title[0]}-${item.title[1]}` }}) 
        } : null ),
        ...(['radio','checkbox'].includes(question.question_type)? { answer_option: answerForm[question.id] } : null),
      }

      delete body.id; // some question id will be newly generated in the frontend so we delete it before sending it to api to not confuse it as existing question

      if(question.is_new){
        new_question_arr.push(body);
        return;
      }

      if(question.is_modified){
        body.id = question.id // add id back when the question is modified instead of newly created and the id exist in api
        modified_question_arr.push(body);
        return;
      }
    })

    if(new_question_arr.length > 0){
      await $api('/v1/questions' , {
        method: 'POST',
        body: new_question_arr
      })
    }
    if(modified_question_arr.length > 0){
      await $api('/v1/questions' , {
        method: 'POST',
        body: modified_question_arr
      })
    }
    
  })

}

// initialize the form to make it reactive by dynamically add ref by itself so that we can track if the question or category is updated or untouched
// doing this will let us only call api endpoint depend on each input state, POST if its newly added, PUT or PATCH if its updated or modified
function initializeForm(){
  categoryData.value?.categories?.forEach((category: ICategory) => {
    if(category.type === 'sochanakor'){
      addSochanakor(category,{ is_new: false, is_modified: false })
      
      if(!category.questions) return;
      category.questions.forEach((question: IQuestion)=>{
        addQuestion(question,{ is_new: false, is_modified: false })

        const parsed_answer = JSON.parse(question.answer_option ?? question.rule ?? '[]');

        if(!parsed_answer) return;

        parsed_answer.forEach(answer => {
          if(question.question_type === 'text') {
            return addAnswer(answer,question.id.toString(),question.question_type)
          };
          return addAnswer(answer,question.id.toString());
        })
      })
    }
    else if(category.type === 'standard'){
      addCategory(category,{ is_new: false, is_modified: false })
    }
  })
}

function toggleDescription(sid: number, index: number) {
  const questionList = questionsForm[sid]; // Directly access the reactive object

  if (!questionList || !questionList[index]) return; // Prevent errors if sid or index doesn't exist

  questionList[index].hasDescription = !questionList[index].hasDescription;
}

function toggleQuestionDialog(id?: number){
  isDialogVisible.value = !isDialogVisible.value
  
  if(!id) return;
  selected_question_id.value = id!;
}

async function handleUpdateTitle(){
  isEditTitle.value = !isEditTitle;

  if(titleForm.value.title == categoryData.value.title) return;

  await $api('/v1/category-groups/' + Param?.id, {
    method: 'POST',
    body: titleForm.value
  })
}

watch(categoryData, (newData) => {
  if (newData) initializeForm();
}, { immediate: true });

const question_type = [ 
  { title: 'ប្រភេទប៊ូតុងជ្រើស', value: 'radio' },
  { title: 'ប្រភទប្រអប់ធីក', value: 'checkbox' },
  { title: 'ប្រភទវាលបញ្ចូលអត្ថបទ', value: 'text' },
]

</script>

<template>
  <section>
    <div class="d-flex flex-wrap justify-start justify-sm-space-between gap-y-4 gap-x-6 mb-6">
      <div class="d-flex flex-column justify-center">
        <div class="flex gap-2 items-center text-secondary">
          <h4 v-if="!isEditTitle" class="text-h3 font-weight-medium text-secondary">
            {{ titleForm.title }}
          </h4>
          <IconBtn v-if="!isEditTitle" @click="isEditTitle = !isEditTitle">
            <VIcon icon="tabler-edit" />
          </IconBtn>
          <AppTextField v-if="isEditTitle" v-model="titleForm.title"/>
          <VBtn v-if="isEditTitle" variant="tonal" size="small" @click="handleUpdateTitle">រក្សាទុក</VBtn>
          
        </div>
        <div class="text-lg text-secondary">
          ជ្រើសរើសសំណួរដែលអ្នកចង់ប្រមូលព័ត៌មាន
        </div>
      </div>

      <div class="d-flex gap-4 align-center flex-wrap scale-95">
        <VBtn size="large" variant="tonal" color="secondary" to="/assessment/category">
          បោះបង់ចោល
        </VBtn>
        <VBtn size="large" variant="tonal" color="primary" @click="submit">
          រក្សាទុកសិន
        </VBtn>
        <VBtn size="large">ដាក់អោយដំណើរការ</VBtn>
      </div>
    </div>
  </section>


  <VCard class="text-secondary">
    <VCardTitle>
      <h1 class="text-lg my-4 text-secondary">កម្រងសំណួរ</h1>
    </VCardTitle>
    
    <!-- layer 1: layout -->
    <VCardText>
      <div class="border-[1px] border-gray-400/60 rounded-sm">
        <!-- the card title -->
        <div class="flex w-full px-2 border-b-[1px] border-b-gray-400/60">
          <span class="w-fit border-r py-5 pr-3 border-gray-400/60">ល.រ</span>
          <span class="flex flex-1 items-center justify-center">ស្តង់ដា/សូចនករ/សំណួរ</span>
        </div>
        
        <!-- card content -->
        <VList class="overflow-hidden w-full text-secondary">
          <!-- Layer 1: Category -->
          <template v-for="([key, category], index) in Object.entries(categoriesForm)" :key="category.id" >
            <VRow>
              <div class="flex flex-1 gap-4">
                <span class="ml-7 h-full pt-5 w-[1ch] text-center text-nowrap">
                  {{ convertToKhmerNumerals((index+1).toString()) }}
                </span>
                <VListGroup class="w-full border-l border-gray-400/60 pl-2">
                  <template #activator="{ props }">
                    <div class="flex gap-2 py-2 items-center text-lg">
                      <VIcon size="small" icon="tabler-pencil-star" />
                      <span>ស្តង់ដារ</span>
                      <AppTextField @input="setCategoryModified(key)" v-model.lazy="category.title" placeholder="ស្តង់ដា" class="flex flex-1" />
                      <VListItem v-bind="props" variant="plain"></VListItem>
                    </div>
                  </template>

                  <!-- Layer 2: Sochanakor -->
                  <template v-for="(sochanakor, jndex) in sochanakorForm[category.id]?? sochanakorForm[key]?? []" :key="sochanakor.id">
                    <VRow>
                      <div class="flex flex-1">
                        <span class="ml-3 h-full pt-6 w-[4ch] text-center text-nowrap pr-3 border-r border-gray-400/60">
                          {{ convertToKhmerNumerals(`${index+1}.${jndex+1}`) }}
                        </span>
                        <VCol>
                          <VListGroup>
                            <template #activator="{ props }">
                              <div class="flex gap-2 pb-2 items-center text-lg">
                                <VIcon size="small" icon="tabler-edit" />
                                <span>សូចនករ</span>
                                <AppTextField @input="setSochanakorModified(key)" v-model.lazy="sochanakor.title" placeholder="សូចនករ" class="flex flex-1" />
                                <VListItem v-bind="props" variant="plain"></VListItem>
                              </div>
                            </template>

                            <!-- Layer 3: Question -->
                            <template v-for="(question, kndex) in questionsForm[sochanakor.id]?? []" :key="question.id">
                              <VRow>
                                <div class="flex flex-1">
                                  <span class="ml-1 h-full pt-6 pr-3 w-[6ch] text-center text-nowrap text-sm border-r-2 border-r-[#FF830F]/60">
                                    {{ convertToKhmerNumerals(`${index+1}.${jndex+1}.${kndex+1}`) }}
                                  </span>
                                  <VCol>
                                    <VListGroup class="flex flex-col flex-auto">
                                      <template #activator="{ props }">
                                        <div class="flex gap-2 items-center text-lg">
                                          <VIcon size="small" icon="tabler-pencil-question" />
                                          <span>សំណួរ</span>
                                          <AppTextField @input="setQuestionModified(sochanakor.id,kndex)" v-model.lazy="question.question" placeholder="សំណួរ" class="flex flex-1" />
                                          <VBtn @click="toggleQuestionDialog(question.id)" class="group" 
                                              v-if="typeof question.id != 'string'" 
                                              size="small" variant="text" color="grey-800" append-icon="tabler-square-plus">
                                            <span class="hidden group-hover:block">ចំណុចផ្ទៀងផ្ទាត់</span>
                                          </VBtn>
                                          <VListItem v-bind="props" variant="plain" />
                                        </div>
                                      </template>

                                      <!-- Answer Section -->
                                      <VRow>
                                        <VCol cols="12" class="flex mt-2 flex-col items-start pb-8 text-base">
                                          <VBtn v-if="!question.hasDescription" @click="toggleDescription(sochanakor.id, kndex)" variant="plain" size="small">
                                            ចំណុចផ្ទៀងផ្ទាត់
                                          </VBtn>

                                          <VRow class="w-full">
                                            <VCol cols="12">
                                              <AppTextField 
                                                @input="setQuestionModified(sochanakor.id,kndex)"
                                                v-model.lazy="question.description"
                                                v-if="question.hasDescription"
                                                class="flex flex-1 w-full" density="compact" 
                                                placeholder="របាយការណ៍ចុងឆ្នាំសិក្សាដោយមានស្ថិតិសិស្សបោះបង់ការសិក្សា">
                                                <template #append>
                                                  <IconBtn size="small" variant="plain" @click="toggleDescription(sochanakor.id, kndex)">
                                                    <VIcon icon="tabler-x" />
                                                  </IconBtn>
                                                </template>
                                              </AppTextField>
                                            </VCol>
                                          </VRow>

                                          <VRow class="px-2 pr-15 w-full flex justify-between gap-2">
                                            <span v-if="['radio','checkbox'].includes(question.question_type)" class="text-lg">ចម្លើយ៖</span>
                                            <span v-if="['text'].includes(question.question_type)" class="text-lg">លក្ខខ័ណ្ទ</span>
                                            <!-- answer type -->
                                            <AppSelect
                                              v-model="question.question_type"
                                              @input="onQuestionTypeChange(question.id)"
                                              :items="question_type" density="compact" 
                                              class="max-w-40" placeholder="ប្រភេទជ្រើសរើស"/>
                                          </VRow>

                                          <!-- Answer Choices -->
                                          <VRow v-if="['radio','checkbox'].includes(question.question_type)" class="w-full pl-2 pr-15 flex flex-col pt-1">
                                            <template v-for="(answer, lndex) in answerForm[question.id]?? []">
                                              <div class="flex text-center">
                                                <span class="w-[2ch] flex items-center">{{ resolveKhmerLetters(lndex) }}.</span>
                                                <input @input="setQuestionModified(sochanakor.id,kndex)" class="border outline-[#FF830F] py-1 px-2 flex flex-1 items-center" v-model="answer.title"/>
                                                <div class="border py-1 px-2 flex flex-1 max-w-1/5 items-center">
                                                  <span class="mr-2">ពិន្ទុ</span>
                                                  <AppTextField @input="setQuestionModified(sochanakor.id,kndex)" v-model="answer.point" density="compact" />
                                                  <IconBtn size="small" variant="plain" @click="removeAnswer(question.id,lndex)">
                                                    <VIcon icon="tabler-x" />
                                                  </IconBtn>
                                                </div>
                                              </div>
                                              <div class="pt-2 pl-4">
                                                <VBtn @click="addAnswer(undefined,question.id)"
                                                  v-if="lndex+1 == answerForm[question.id].length" 
                                                  variant="tonal" size="small" prepend-icon="tabler-plus">បន្ថែមចម្លើយ</VBtn>
                                              </div>
                                            </template>
                                          </VRow>

                                          <!-- text input type -->
                                          <VRow v-if="['text'].includes(question.question_type)" class="w-full pl-2 pr-15 flex flex-col pt-1">
                                            <div class="grid grid-cols-5 mb-1" v-if="answerForm[question.id].length > 0">
                                              <span class="col-span-2 pl-1">ចាប់ពី</span>
                                              <span class="col-span-2 pl-1">ដល់</span>
                                              <span class="col-span-1 pl-1">ពិន្ទុ</span>
                                            </div>
                                            <template v-for="(answer, lndex) in answerForm[question.id]?? []">
                                              <div class="text-center grid grid-cols-5">
                                                <input @input="setQuestionModified(sochanakor.id,kndex)" class="border outline-[#FF830F] py-1 px-2 flex flex-1 items-center col-span-2" v-model="answer.title[0]"/>
                                                <input @input="setQuestionModified(sochanakor.id,kndex)" class="border outline-[#FF830F] py-1 px-2 flex flex-1 items-center col-span-2" v-model="answer.title[1]"/>
                                                <div class="border py-1 px-2 flex items-center col-span-1">
                                                  <AppTextField @input="setQuestionModified(sochanakor.id,kndex)" v-model="answer.point" density="compact" />
                                                  <IconBtn size="small" variant="plain" @click="removeAnswer(question.id,lndex)">
                                                    <VIcon icon="tabler-x" />
                                                  </IconBtn>
                                                </div>
                                              </div>

                                              <div class="pt-2 pl-4">
                                                <VBtn @click="addAnswer(undefined,question.id, 'text')"
                                                  v-if="lndex+1 == answerForm[question.id].length" 
                                                  variant="tonal" size="small" prepend-icon="tabler-plus">បន្ថែមចម្លើយ</VBtn>
                                              </div>
                                            </template>
                                          </VRow>


                                          <VRow>
                                            <div class="pt-2 pl-4">
                                              <VBtn @click="addAnswer(undefined,question.id, question.question_type)"
                                                v-if="!answerForm[question.id] || answerForm[question.id].length === 0" 
                                                variant="tonal" size="small" prepend-icon="tabler-plus">បន្ថែមចម្លើយ</VBtn>
                                            </div>
                                          </VRow>

                                        </VCol>
                                      </VRow>
                                    </VListGroup>
                                  </VCol>
                                </div>
                              </VRow>

                              <VRow v-if="kndex + 1 === (questionsForm[sochanakor.id]?.length || 0)">
                                <div class="flex gap-4 pl-1 py-3 items-center">
                                  <span class="flex items-end text-sm">{{ convertToKhmerNumerals(`${index+1}.${jndex+1}.${kndex+2}`) }}</span>
                                  <VBtn @click="addQuestion(undefined,{ is_new: true, is_modified: false }, sochanakor.id)" variant="elevated" size="small" prepend-icon="tabler-plus">បន្ថែមសំណួរ</VBtn>
                                </div>
                              </VRow>
                            </template>
                            <VRow v-if="!questionsForm[sochanakor.id] || questionsForm[sochanakor.id].length === 0">
                              <div class="flex gap-4 pl-1 py-3 items-center">
                                <span class="flex items-end text-sm">{{ convertToKhmerNumerals(`${index+1}.${jndex+1}.1`) }}</span>
                                <VBtn @click="addQuestion(undefined,{ is_new: true, is_modified: false },sochanakor.id)" variant="elevated" size="small" prepend-icon="tabler-plus">បន្ថែមសំណួរ</VBtn>
                              </div>
                            </VRow>
                          </VListGroup>
                        </VCol>
                      </div>
                    </VRow>

                    <VRow v-if="jndex+1 === (sochanakorForm[category.id]?.length || 0)">
                      <VCol class="flex gap-4 items-center mb-4">
                        <span class="flex items-end">{{ convertToKhmerNumerals(`${index+1}.${jndex+2}`) }}</span>
                        <VBtn @click="addSochanakor(undefined,{ is_new: true, is_modified: false },key)" variant="elevated" size="small" prepend-icon="tabler-plus">បន្ថែមសូចនករ</VBtn>
                      </VCol>
                    </VRow>
                  </template>
                  <VRow v-if="!sochanakorForm[category.id] || sochanakorForm[key].length === 0">
                    <VCol class="flex gap-4 items-center mb-4">
                      <span class="flex items-end">{{ convertToKhmerNumerals(`${index+1}.${sochanakorForm[key]?.length+1 || 1}`) }}</span>
                      <VBtn @click="addSochanakor(undefined,{ is_new: true, is_modified: false },key)" variant="elevated" size="small" prepend-icon="tabler-plus">បន្ថែមសូចនករ</VBtn>
                    </VCol>
                  </VRow>
                </VListGroup>
              </div>
            </VRow>
            <VRow v-if="index+1 === Object.keys(categoriesForm).length ">
              <VCol class="flex gap-2 items-center">
                <span class="px-4 flex items-end">{{ convertToKhmerNumerals((index+2).toString()) }}</span>
                <VBtn @click="addCategory(undefined,{ is_new: true, is_modified: false })" variant="elevated" size="small" prepend-icon="tabler-plus">បន្ថែមស្តង់ដា</VBtn>
              </VCol>
            </VRow>
          </template>
          <VRow v-if="Object.keys(categoriesForm).length == 0">
            <VCol class="flex gap-2 items-center">
              <span class="px-4 flex items-end">{{ convertToKhmerNumerals('1') }}</span>
              <VBtn @click="addCategory(undefined,{ is_new: true, is_modified: false })" variant="elevated" size="small" prepend-icon="tabler-plus">បន្ថែមស្តង់ដា</VBtn>
            </VCol>
          </VRow>
        </VList>

      </div>

    </VCardText>
  </VCard>



  <VDialog v-model="isDialogVisible" max-width="1000">
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />
    <!-- Dialog Content -->
     <QuestionDialog :question_id="selected_question_id!" :toggleDialog="toggleQuestionDialog" />
  </VDialog>
</template>
