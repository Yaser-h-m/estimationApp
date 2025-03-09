<template>
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold">League Table</h1>
            <span>
                <button @click="startSimulation" v-if="!isCompleted" class="bg-blue-500 text-white px-4 py-2 rounded">Start Simulation</button>

            </span>
        </div>
        <div class="row w-full flex flex-warp  items-center">
            <WeekMatches v-for="week, index in matches" :matches="week" :current-week="index" :total-weeks="totalWeeks"
                :can-predict="canPredict" @play-all="playAll" @play-week="playWeek" @reset="reset"
                @update-match="updateMatch" />
        </div>

        <Link :href="route('league.start-simulation')">Start Simulation</Link>
    </div>
</template>

<script>

import { defineComponent } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';

import LeagueTable from './LeagueTable.vue';
import MatchList from './MatchList.vue';
import WeekMatches from './WeekMatches.vue';
import { comma } from 'postcss/lib/list';

export default defineComponent({
    components: {
        LeagueTable,
        MatchList,
        WeekMatches
    },
    props: {
        matches: Array,
        currentWeek: Number,
        totalWeeks: Number,
        canPredict: Boolean,
        isCompleted: Boolean
    },
    methods: {
        startSimulation() {
            router.get(route('league.start-simulation'));
        },
    },
    mounted() {
        console.log('League Index Component Loaded');
        console.log(this.matches);
    },
});

</script>