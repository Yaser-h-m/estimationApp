<!-- resources/js/Components/MatchList.vue -->
<template>
    <div class="space-y-2">
      <div v-for="match in matches" :key="match.id" class="p-3 border rounded">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <span class="font-medium">{{ match.home_team }}</span>
            <template v-if="match.played">
              <span class="text-lg">{{ match.home_goals }}</span>
              <span>-</span>
              <span class="text-lg">{{ match.away_goals }}</span>
            </template>
            <template v-else>
              <span class="text-sm text-gray-500">vs</span>
            </template>
            <span class="font-medium">{{ match.away_team }}</span>
          </div>
          
          <template v-if="match.played && !isEditing(match.id)">
            <button 
              v-if="!match.played"
              @click="startEditing(match)"
              class="text-blue-500 hover:text-blue-700"
            >
              Set result
            </button>
            <button 
              v-else
              @click="startEditing(match)"
              class="text-blue-500 hover:text-blue-700"
            >
              Edit
            </button>
          </template>
        </div>
        
        <!-- Edit form -->
        <div v-if="isEditing(match.id)" class="mt-2">
          <div class="flex items-center space-x-2">
            <input 
              type="number" 
              v-model="editForm.home_goals" 
              min="0"
              class="w-16 p-1 border rounded"
            />
            <span>-</span>
            <input 
              type="number" 
              v-model="editForm.away_goals" 
              min="0"
              class="w-16 p-1 border rounded"
            />
            <button 
              @click="saveEdit(match)"
              class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
            >
              Save
            </button>
            <button 
              @click="cancelEdit"
              class="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      matches: Array
    },
    data() {
      return {
        editingMatchId: null,
        editForm: {
          home_goals: 0,
          away_goals: 0
        }
      };
    },
    methods: {
      isEditing(matchId) {
        return this.editingMatchId === matchId;
      },
      startEditing(match) {
        this.editingMatchId = match.id;
        this.editForm.home_goals = match.played ? match.home_goals : 0;
        this.editForm.away_goals = match.played ? match.away_goals : 0;
      },
      cancelEdit() {
        this.editingMatchId = null;
      },
      saveEdit(match) {
        this.$emit('update-match', match, this.editForm.home_goals, this.editForm.away_goals);
        this.editingMatchId = null;
      }
    }
  };
  </script>