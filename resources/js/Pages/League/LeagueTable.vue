<!-- resources/js/Components/LeagueTable.vue -->
<template>
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-100">
            <th class="py-2 px-3 text-left">Pos</th>
            <th class="py-2 px-3 text-left">Team</th>
            <th class="py-2 px-3 text-center">P</th>
            <th class="py-2 px-3 text-center">W</th>
            <th class="py-2 px-3 text-center">D</th>
            <th class="py-2 px-3 text-center">L</th>
            <th class="py-2 px-3 text-center">GF</th>
            <th class="py-2 px-3 text-center">GA</th>
            <th class="py-2 px-3 text-center">GD</th>
            <th class="py-2 px-3 text-center">Pts</th>
            <th class="py-2 px-3 text-center">win chance %</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(entry, index) in teams" :key="entry.id" :class="{'bg-gray-50': index % 2 === 0}">
            <td class="py-2 px-3">{{ index + 1 }}</td>
            <td class="py-2 px-3 font-medium">{{ entry.name }}</td>
            <td class="py-2 px-3 text-center">{{ entry.played }}</td>
            <td class="py-2 px-3 text-center">{{ entry.won }}</td>
            <td class="py-2 px-3 text-center">{{ entry.drawn }}</td>
            <td class="py-2 px-3 text-center">{{ entry.lost }}</td>
            <td class="py-2 px-3 text-center">{{ entry.goals_for }}</td>
            <td class="py-2 px-3 text-center">{{ entry.goals_against }}</td>
            <td class="py-2 px-3 text-center">{{ entry.goal_difference }}</td>
            <td class="py-2 px-3 text-center font-bold">{{ entry.points }}</td>
            <td class="py-2 px-3 text-center font-bold">{{ entry.win_percentage }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      teams: Array
    }
  };
  </script>