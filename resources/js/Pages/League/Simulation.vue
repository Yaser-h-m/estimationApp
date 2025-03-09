<template>
    
    <h1 class="text-2xl font-semibold container">Simulation</h1>
    <div class="flex container items-center " >
        <div class="w-1/2 mx-auto">
            
            <LeagueTable :teams="teams" />
        </div>
        
        <WeekMatches v-if="!isCompleted" :matches="weekMatches" :currentWeek="currentWeek"/>

    </div>
    <div class="buttons flex mt-4">
        <button v-if="!isCompleted" @click="playNextWeek" class="bg-blue-500 text-white mx-2 px-4 py-2 rounded">Play Next Week</button>
        <button v-if="!isCompleted" @click="playAllWeeks" class="bg-blue-500 text-white mx-2 px-4 py-2 rounded">Play All Weeks</button>
        <button @click="Reset" class="bg-red-500 text-white px-4 mx-2 py-2 rounded">Reset</button>
    </div>

    <div class="container mt-4">
        <h1>Played Matches</h1>
        
        <div class="flex flex-wrap">
            <WeekMatches v-for="week, index in matches" :matches="week" :current-week="index" />
        </div>
    </div>
</template>

<script>

import { defineComponent } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

import LeagueTable from './LeagueTable.vue';
import WeekMatches from './WeekMatches.vue';


export default defineComponent({
    components: {
        LeagueTable,
        WeekMatches
    },
    props: {
        teams: Array,
        weekMatches: Array,
        currentWeek: Number    ,
        matches: Array,
        isCompleted: Boolean
    },
    methods:{
        playNextWeek(){
            router.get(route('league.play-next-week'));
        },
        playAllWeeks(){
            router.get(route('league.play-all-games'));
        },
        Reset(){
            router.get(route('league.initialize'));
        }
    },
    mounted() {
        console.log('League Index Component Loaded');
        console.log(this.teams);
    },
});

</script>