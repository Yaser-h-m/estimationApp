<template>

<div class="container mx-auto p-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Pot Teams</h1>
        <span>
            <button @click="ScheduledMatches" class="bg-blue-500 text-white px-4 py-2 rounded">Start fixture</button>
        </span>
        </div>

    <h1> Pot Teams</h1>
    <div class="w-1/2 mx-auto">
        <LeagueTable :teams="teams" />
    </div>
</div>
</template>

<script>

import { defineComponent } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

import LeagueTable from './LeagueTable.vue';
import ScheduledMatches from './ScheduledMatches.vue';

export default defineComponent({
    components: {
        LeagueTable,
    },
    props: {
        teams: Array,
    },
    methods:{
        ScheduledMatches(){
            router.get(route('league.initialize'));
        }
    },
    mounted() {
        console.log('League Index Component Loaded');
        console.log(this.teams);
    },
});

</script>