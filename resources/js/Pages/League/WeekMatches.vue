<template>
    
    <div class="col-2 border-1 border-black pt-4 w-1/3  p-6">
        <h3 class="font-semibold mb-2 p-2 bg-gray-500">Week {{ currentWeek }} </h3> 
        <MatchList :matches="matches" @update-match="updateMatch" />
    </div>
    
</template>
<script>
import { defineComponent } from 'vue';  
import { router  } from '@inertiajs/vue3';
import MatchList from './MatchList.vue';

export default defineComponent({
    components: {
        MatchList,
    },
    props: {
        matches: Array,
        currentWeek: Number,
    },
    methods: {
        updateMatch(match, homeGoals, awayGoals) {
            router.put(route('league.update-game', match), {
                home_goals: homeGoals,
                away_goals: awayGoals
            });
        }
    }
});

</script>