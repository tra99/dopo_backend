<script lang="ts" setup>
import { router } from '@/plugins/1.router';
import Challenge from '../mission/(component)/challenge.vue';
import Conclusion from '../mission/(component)/conclusion.vue';
import Participant from '../mission/(component)/participant.vue';
import Progress from '../mission/(component)/progress.vue';

const isEdit = ref(false);
const isRefresh = ref(false);

const schoolParam: any = router.currentRoute.value.params
const { data: missionData, execute } = await useApi<any>(createUrl('/v1/schools/1762'));
// const { data: missionData, execute } = await useApi<any>(createUrl('/v1/schools/'+schoolParam.id));

//form reference
const participantFormRefs = ref<any>();
const conclusionFormRefs = ref<any>();
const challengeFormRefs = ref<any>();
const progressFormRefs = ref<any>();

// Current mission
const currentMission = ref(0);
const currentMissionData = computed(() => missionData.value?.missions?.[currentMission.value]);


const submitAllForms = async () => {
    isEdit.value = !isEdit.value;

    await participantFormRefs.value?.submitForm();
    await progressFormRefs.value?.submitForm();
    await challengeFormRefs.value?.submitForm();
    await conclusionFormRefs.value?.submitForm();

    isRefresh.value = !isRefresh.value;
    setTimeout(() => {  
        isRefresh.value = !isRefresh.value;
    }, 500);
};
function reset(){
    isEdit.value = !isEdit.value;
    execute();
}

function nextMission(){
    if((currentMission.value +1 ) == missionData?.value.missions.length) return;
    currentMission.value++;
}

function previousMission(){

    if(currentMission.value == 0) return;
    currentMission.value--;
}
</script>

<template>
  <VRow>
    <VCol cols="12" v-if="missionData?.missions.length == 0">
        <VCard>
            <VCardText>
              <span> សាលារៀន មិនទាន់មានបេសកកម្ម</span>
            </VCardText>
        </VCard>
    </VCol>
    <!-- the header of the view -->
    <VCol cols="12" v-else-if="currentMissionData" >
        <VRow>
            <VCol cols="12">
                <VCard>
                    <VCardTitle class="border-b pb-8 mt-6">
                        <VRow justify="space-between" class="items-center">
                            <VCol cols="6">
                                <div class="flex gap-3 items-center">
                                    <VBtn @click="previousMission()" icon="tabler-chevron-left" variant="text" color="secondary"/>  
                                    <div>
                                        <h1 class="text-lg text-secondary font-semibold">កាលបរិច្ឆេទ៖ {{ currentMissionData && resolveDate(currentMissionData?.created_at) }}</h1>
                                        <span class="text-base text-secondary">អ្នកចុះគាំទ្រ៖ {{ convertToKhmerNumerals(currentMissionData?.participants?.length) }} នាក់</span>
                                    </div>
                                </div>
                            </VCol>
                            <VCol cols="6">
                                <div class="flex gap-1 items-center pr-3 justify-end">
                                    <VBtn @click="nextMission()" icon="tabler-chevron-right" variant="text" color="secondary"/>  
                                </div>
                            </VCol>
                        </VRow>
                    </VCardTitle>
                    <VCol>
                        <VRow>
                            <VCol cosl="12">
                                <VBtn icon="tabler-trash" variant="text" color="secondary"/>  
                                <VBtn icon="tabler-download" variant="text" color="secondary"/>  
                            </VCol>
                        </VRow>
                    </VCol>
                </VCard>
            </VCol>

            <!-- the mission report of the school -->

            <VCol cols="12" class="relative">
                <!-- not working -->
                <VRow v-if="missionData?.missions.length > 1" class="mt-12"> 
                    <VCol cols="10" class="absolute w-full bottom-[95%] opacity-70 right-1/2 translate-x-1/2">
                        <VCard height="40"></VCard>
                    </VCol>
                    <VCol cols="11" class="absolute w-full bottom-[93%] opacity-80 right-1/2 translate-x-1/2">
                        <VCard height="40"></VCard>
                    </VCol>
                </VRow>

                <VCard>
                    <VCardTitle class="border-b pb-8 mt-6 mb-4">
                        <VRow justify="space-between" class="items-center">
                            <VCol cols="6">
                                <div class="flex gap-3 items-center">
                                    <VIcon color="secondary" size="52" icon="tabler-clipboard-plus"/>
                                    <h1 class="text-lg text-secondary font-semibold">របាយការណ៍ និង លិទ្ធផល</h1>
                                </div>
                            </VCol>
                            <VCol cols="6">
                                <div class="flex gap-1 items-center pr-3 justify-end">
                                    <h1 class="text-base text-secondary mr-2">កែប្រែចុងក្រោយ៖ {{ resolveDate(missionData?.updated_at!) }}</h1>
                                    <!-- <VBtn icon="tabler-paperclip" variant="text" color="secondary"/>  -->
                                    <VBtn icon="tabler-edit" @click.stop="isEdit = !isEdit" :variant="isEdit? 'tonal':'text'" :color="isEdit? 'primary':'secondary'"/> 
                                </div>
                            </VCol>
                        </VRow>
                    </VCardTitle>

                    <VCardText>
                        <VRow v-if="!isRefresh">
                            <!-- Participant -->
                            <Participant
                            ref="participantFormRefs"
                            :schools="[missionData]" 
                            :is_edit="isEdit"
                            :mission_id="currentMissionData?.id!"/>

                            <!-- Progress -->
                            <Progress
                            ref="progressFormRefs"
                            :is_edit="isEdit" 
                            :schools="[missionData]"
                            :mission_id="currentMissionData?.id!"/>

                            <!-- Challenge -->
                            <Challenge
                            ref="challengeFormRefs"
                            :is_edit="isEdit" 
                            :schools="[missionData]"
                            :mission_id="currentMissionData?.id!"/>

                            <!-- reflection and Conclusion of the participant -->
                            <Conclusion
                            ref="conclusionFormRefs"
                            :is_edit="isEdit"
                            :missions="currentMissionData"
                            :schools="[missionData]"
                            :hide_file="true"/>
                        </VRow>

                        <!-- submit or update button  -->
                        <VCol>
                            <VRow class="gap-3 justify-end">
                                <VBtn color="secondary" variant="tonal" v-if="isEdit" class="mt-4" @click="reset()">បោះបង់ចោល</VBtn>
                                <VBtn prepend-icon="tabler-send" color="primary" v-if="isEdit" class="mt-4" @click="submitAllForms()">រក្សាទុក</VBtn>
                            </VRow>
                        </VCol>
                    </VCardText>
                </VCard>
            </VCol>
            
        </VRow>
        
    </VCol>
  </VRow>
</template>
