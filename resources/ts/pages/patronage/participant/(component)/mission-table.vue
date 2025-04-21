<script lang="ts" setup>
import { IParticipant } from '../interface';


const prop = defineProps({
    participant: {
        type: Object,
        required: true
    }
})

const participant = computed((): IParticipant => prop.participant as IParticipant)

const search = ref<string>('');
const itemsPerPage = ref<number>(10)
const page = ref<number>(1)

const query = {
    search,
    by_participants_id: prop.participant.id,
    limit: itemsPerPage,
    page,
    sort_direction: 'asc',
}
const { data: missionsData } = await useApi<any>(createUrl('/v1/missions',{ query }));
const missions = computed((): any[] => missionsData.value.data)

const resolveStatusVariant = (stat: string) => {
  if (stat == 'pending')
    return 'warning'
  else  return 'success'
}

const headers = [
  { title: '#', key: 'id' , sortable: false}, 
  { title: 'សាលារៀន', key: 'school_name', sortable: false },
  { title: 'ទីតាំង', key: 'province', sortable: false },
  { title: 'កាលបរិច្ឆេទ', key: 'date' , sortable: false},
  { title: 'ចំនួនក្រុមការងារ', key: 'participants', sortable: false },
  { title: 'ស្ថានភាព', key: 'status', sortable: false },
  { title: 'សកម្មភាព', key: 'actions', sortable: false },
]

const status = [
  { title: 'បានបញ្ចប់ជាស្ថាពរ', value: 'null' },
  { title: 'មិនមានរបាយការណ៍', value: 'pending' },
]
</script>

<template>
    <VCard>
        <VCardText class="flex gap-2 justify-between items-center">
            <h4 class="text-h5 text-secondary text-center text-sm-start mb-2">បេសកកម្មដែលបានចូលរួម</h4>
            <VCol cols="5">
                <AppTextField v-model="search" 
                placeholder="ស្វែងរក" dense outlined single-line append-inner-icon="tabler-search" clear-icon="tabler-x"/>
            </VCol>
        </VCardText>
        <VCardText>
            <VDataTableServer
                v-model:items-per-page="itemsPerPage"
                v-model:page="page"
                :items="missions"
                :items-length="missions.length"
                :headers="headers"
                class="text-secondary"
                show-select>
                <!-- # -->
                <template #item.id="{ item }">
                <span class="text-primary">
                    {{ item?.id }}
                </span>
                </template>


                <template #item.school_name="{ item }">
                    <div v-if="item.schools.length < 3" class="flex flex-col gap-1 justify-center text-nowrap">
                        <span v-for="school in item?.schools">{{ school.school_name_kh }}</span>
                    </div>
                    <div v-if="item.schools.length > 2" class="flex gap-2 items-center">
                        <div class="flex flex-col gap-1 justify-center text-nowrap">
                        <span>{{ item.schools[0].school_name_kh }}</span>
                        <span>{{ item.schools[1].school_name_kh }}</span>
                        </div>
                        <VBadge :offset-x="-18" class="v-badge--tonal cursor-pointer" :content="`+${item.schools.length -2}`">
                        <VTooltip class="opacity-90" location="top" activator="parent" open-on-focus>
                            FF830F      <div class="flex flex-col">
                            <span v-for="school in item?.schools.slice(2)">{{ school.school_name_kh }},</span>
                            </div> 
                        </VTooltip>
                        </VBadge>
                    </div>
                </template>


                <template #item.province="{ item }">
                    <div v-if="item.schools.length < 3" class="flex flex-col gap-1 justify-center text-nowrap">
                        <span v-for="school in [...new Set(item?.schools.map(s => s.province_kh))]">
                        {{ school }}
                        </span>
                    </div>
                    <div v-if="item.schools.length > 2" class="flex gap-2 items-center">
                        <div class="flex flex-col gap-1 justify-center text-nowrap">
                        <span>{{ [...new Set(item.schools.map(s => s.province_kh))][0] }}</span>
                        <span v-if="[...new Set(item.schools.map(s => s.province_kh))][1] !== [...new Set(item.schools.map(s => s.province_kh))][0]">
                            {{ [...new Set(item.schools.map(s => s.province_kh))][1] }}
                        </span>
                        </div>
                        <VBadge v-if="[...new Set(item.schools.map(s => s.province_kh))].length > 2"
                        :offset-x="-18" 
                        class="v-badge--tonal cursor-pointer" 
                        :content="`+${Math.max([...new Set(item.schools.map(s => s.province_kh))].length - 2, 0)}`"
                        >
                        <VTooltip class="opacity-90" location="top" activator="parent" open-on-focus>
                            <div class="flex flex-col">
                            <span v-for="school in [...new Set(item?.schools.map(s => s.province_kh))].slice(2)">
                                {{ school }},
                            </span>
                            </div>
                        </VTooltip>
                        </VBadge>
                    </div>
                </template>


                <!-- 👉 date -->
                <template #item.date="{ item }">
                    <div class="d-flex align-center gap-x-2 opacity-70">
                        <div>
                        {{ resolveDate([item.start_date, item.end_date]) }}
                        </div>
                    </div>
                </template>


                <!-- 👉 date -->
                <template #item.participants="{ item }">
                    <div class="d-flex align-center gap-x-2">
                        <div>
                        {{ item.participants.length || 0 }} នាក់
                        </div>
                    </div>
                </template>

                <!-- Status -->
                <template #item.status="{ item }">
                    <VChip
                        :color="resolveStatusVariant(item.status)"
                        size="small"
                        label
                        class="text-capitalize"
                    >
                    {{ item.status ? status?.find(s => s.value === item.status)?.title : '' }}

                    </VChip>
                </template>

                <!-- Actions -->
                <template #item.actions="{ item }">

                    <IconBtn :to="{ name: 'patronage-mission-id', params: { id: item.id } }">
                        <VIcon icon="tabler-eye" />
                    </IconBtn>
                    </template>

                    <!-- pagination -->
                    <template #bottom>
                    <TablePagination
                        v-model:page="page"
                        :items-per-page="+itemsPerPage"
                        :total-items="participant.missions.length"
                    />
                </template>
            </VDataTableServer>
        </VCardText>
    </VCard>
    
</template>
