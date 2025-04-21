<script lang="ts" setup>
import { router } from '@/plugins/1.router';
import About from './(component)/about.vue';
import MissionTable from './(component)/mission-table.vue';
import { IParticipant } from './interface';


const participantParam: any = router.currentRoute.value.params


const { data: participantData } = await useApi<any>(createUrl('/v1/participants/' + participantParam?.id ));

const participant = computed((): IParticipant => participantData.value)


</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard v-if="participant">
            <div class="h-12 bg-primary opacity-80"></div>

            <VCardText class="d-flex align-bottom flex-sm-row flex-column justify-center gap-x-6">
                <div class="d-flex h-20">
                    <VAvatar rounded size="130" 
                        :image="participant?.avatar || '/images/avatars/default-avatar.png'"
                        class="user-profile-avatar mx-auto"/>
                </div>

                <div class="user-profile-info w-100 mt-16 pt-6 pt-sm-0 mt-sm-0">
                    <h4 class="text-h4 text-secondary text-center text-sm-start mb-2">{{ `${participant?.title || ''} ${participant?.name}` }}</h4>
                    <h4 class="text-base text-secondary text-center text-sm-start opacity-70 mb-2">{{ participant?.position || '' }}</h4>
                </div>
            </VCardText>
        </VCard>
    </VCol>

    <VCol>
        <VRow>
            <!-- user about detail -->
            <VCol cols="12" lg="4">
                <About :participant="participant" />
            </VCol>

            <!-- mission table and alert -->
            <VCol>
                <VRow>
                    <VCol v-if="participant.missions.length != 0">
                        <VCard variant="tonal" color="primary">
                            <VCardTitle class="opacity-70">
                                <VCol>
                                    <VRow>
                                        <VCol cols="12" class="flex gap-4 text-primary">
                                            <div class=" w-8 h-8 justify-center bg-primary flex items-center rounded-lg">
                                                <VIcon icon="tabler-urgent" size="24" />
                                            </div>
                                            <h3 class="text-primary">កិច្ចការបន្ទាន់</h3>
                                        </VCol>
                                    </VRow>
                                    <VRow class="items-center">
                                        <VCol class="text-primary items-center flex gap-3 pl-16">
                                            <VIcon icon="tabler-alert-triangle" size="24" />
                                            <h3 class="text-primary">{{ participant.missions[0]?.purpose }}</h3>
                                        </VCol>
                                        <VCol cols="2">
                                            <VBtn variant="elevated">ធ្វើឥឡូវនេះ</VBtn>
                                        </VCol>
                                    </VRow>
                                </VCol>
                            </VCardTitle>
                        </VCard>
                    </VCol>
                    <VCol cols="12">
                        <MissionTable :participant="participant"/>
                    </VCol>
                </VRow>

            </VCol>
        </VRow>
    </VCol>
</VRow>


</template>

<style lang="scss">
.user-profile-avatar {
  border: 5px solid rgb(var(--v-theme-surface));
  background-color: rgb(var(--v-theme-surface)) !important;
  inset-block-start: -3rem;
}
</style>
