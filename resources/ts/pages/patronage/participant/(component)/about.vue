<script lang="ts" setup>
    defineProps({
        participant: {
            type: Object,
            required: true
        }
    })
    
</script>

<template>
  <VCard>
    <VCardText class="text-secondary opacity-70">
        <!-- general detail about user -->
        <p class="text-[15px] opacity-60">ព័ត៌មានទូទៅ</p>
        
        <!-- organization -->
        <div class="flex align-center pb-3">
            <VIcon
                icon="tabler-crown"
                size="24"
                class="me-2"
            />
            <div class="me-2">អង្គភាព:</div>
            <span>{{ participant?.organization }}</span>
        </div>

        <!-- last login -->
        <div class="flex align-center pb-3">
            <VIcon
                icon="tabler-flag"
                size="24"
                class="me-2"
            />
            <div class="me-2">ចូលប្រព័ន្ធចុងក្រោយ:</div>
            <span>{{ 'មិនដែលចូល' }}</span>
        </div>

        <!-- communication detail about user -->
        <p class="text-[15px] pt-4 opacity-60">ព័ត៌មានទំនាក់ទំនង</p>
        
        <!-- phone -->
        <div class="flex align-center pb-3" v-if="participant?.phone">
            <VIcon
                icon="tabler-phone-call"
                size="24"
                class="me-2"
            />
            <div class="me-2">លេខទូរស័ព្ទ:</div>
            <span>{{ resolvePhonenumber(participant?.phone) }}</span>
        </div>

        <!-- email -->
        <div class="flex align-center pb-3" v-if="participant.email">
            <VIcon
                icon="tabler-mail"
                size="24"
                class="me-2"
            />
            <div class="me-2">អុីម៉េល:</div>
            <span>{{ participant.email }}</span>
        </div>

        <!-- telegram -->
        <div class="flex align-center pb-3" v-if="participant.telegram">
            <VIcon
                icon="tabler-brand-telegram"
                size="24"
                class="me-2"
            />
            <div class="me-2">តេលេក្រាម:</div>
            <span class="text-primary">{{ participant.telegram }}</span>
        </div>

        <!-- address -->
        <div class="flex align-center pb-3" v-if="participant.address">
            <VIcon
                icon="tabler-brand-telegram"
                size="24"
                class="me-2"
            />
            <div class="me-2">អាសយដ្ឋាន:</div>
            <span>{{ participant.address }}</span>
        </div>
    </VCardText>
</VCard>
</template>
