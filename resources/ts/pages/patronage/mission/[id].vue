<script lang="ts" setup>
import { router } from '@/plugins/1.router';
import Challenge from './(component)/challenge.vue';
import Conclusion from './(component)/conclusion.vue';
import Detail from './(component)/detail.vue';
import Participant from './(component)/participant.vue';
import Progress from './(component)/progress.vue';
import { IMission } from './interface';

const isEdit = ref(false);
const isPanelOpen = ref<0 | null>(0);

const missionParam: any = router.currentRoute.value.params
const { data: missionData, execute } = await useApi<IMission>(createUrl('/v1/missions/' + missionParam?.id));
// const { data: missionData, execute } = await useApi<IMission>(createUrl('/v1/missions/1'));

const filterOption = ref('all');
const schoolOption: { title: string; value: string | number }[] = [{ title: 'គ្រប់សាលារៀន', value: 'all'}]

missionData.value?.schools.forEach((school: any,index) => {
    schoolOption.push({ title: school.school_name_kh, value: index })
})

function reset(){
    isEdit.value = !isEdit.value;
    togglePanel();
    setTimeout(() => {
        togglePanel();
    }, 600);
}

// declare form reference and then use them to submit the form from this component
const participantFormRefs = ref<any>();
const conclusionFormRefs = ref<any>();
const challengeFormRefs = ref<any>();
const progressFormRefs = ref<any>();

const submitAllForms = async () => {
    isEdit.value = !isEdit.value;
    await progressFormRefs.value.submitForm();
    await participantFormRefs.value.submitForm();
    await challengeFormRefs.value.submitForm();
    await conclusionFormRefs.value.submitForm();

    execute();
    togglePanel();
    setTimeout(() => {
        togglePanel();
    }, 600);
};

const togglePanel = () => {
  isPanelOpen.value = isPanelOpen.value === 0 ? null : 0; // Toggle between open and closed
};

</script>

<template>
    <VRow>
        <VCol cols="12">
            <Detail :mission="missionData"/>
        </VCol>
        <VCol cols="12">
            <VExpansionPanels variant="default" v-model="isPanelOpen">
                <VExpansionPanel>
                    <VExpansionPanelTitle class="border-b pb-8 mt-6">
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
                                    <!-- <VBtn @click.stop="" icon="tabler-paperclip" variant="text" color="secondary"/>  -->
                                    <VBtn icon="tabler-edit" @click.stop="isEdit = !isEdit" :variant="isEdit? 'tonal':'text'" :color="isEdit? 'primary':'secondary'"/> 
                                </div>
                            </VCol>
                        </VRow>
                    </VExpansionPanelTitle>

                    <!-- the content of the expandable result and evaluation -->
                    <VExpansionPanelText>
                        <VRow justify="end" class="mt-4">
                            <VCol cols="6" md="4" class="flex gap-3 items-center">
                                <span>សំរាប់សាលារៀន</span>
                                <AppSelect v-model="filterOption" :items="schoolOption"/>
                            </VCol>
                        </VRow>

                        <VRow>
                            <!-- Participant -->
                            <Participant
                            ref="participantFormRefs"
                            :schools="filterOption == 'all' ? missionData?.schools : [missionData?.schools[filterOption]]" 
                            :is_edit="isEdit"
                            :mission_id="missionData?.id!"/>

                            <!-- Progress -->
                            <Progress
                            ref="progressFormRefs"
                            :is_edit="isEdit" 
                            :schools="filterOption == 'all' ? missionData?.schools : [missionData?.schools[filterOption]]"
                            :mission_id="missionData?.id!"/>

                            <!-- Challenge -->
                            <Challenge
                            ref="challengeFormRefs"
                            :is_edit="isEdit" 
                            :schools="filterOption == 'all' ? missionData?.schools : [missionData?.schools[filterOption]]"
                            :mission_id="missionData?.id!"/>

                            <!-- reflection and Conclusion of the participant -->
                            <Conclusion
                            ref="conclusionFormRefs"
                            :is_edit="isEdit"
                            :missions="missionData"
                            :schools="filterOption == 'all' ? missionData?.schools : [missionData?.schools[filterOption]]"/>
                        </VRow>

                        <!-- submit or update button  -->
                        <VCol>
                            <VRow class="gap-3 justify-end pb-10">
                                <VBtn color="secondary" variant="tonal" v-if="isEdit" class="mt-4" @click="reset()">បោះបង់ចោល</VBtn>
                                <VBtn prepend-icon="tabler-send" color="primary" v-if="isEdit" class="mt-4" @click="submitAllForms()">រក្សាទុក</VBtn>
                            </VRow>
                        </VCol>

                    </VExpansionPanelText>
                </VExpansionPanel>
            </VExpansionPanels>
        </VCol>
    </VRow>
</template>
