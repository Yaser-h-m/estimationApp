<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeagueService
{
    public function scheduleGamees(): void
    {
        $teams = Team::all();

        if ($teams->count() < 2) {
            return;
        }

        DB::table('games')->truncate();

        // Reset team statistics
        foreach ($teams as $team) {
            $team->played = 0;
            $team->won = 0;
            $team->drawn = 0;
            $team->lost = 0;
            $team->goals_for = 0;
            $team->goals_against = 0;
            $team->points = 0;
            $team->win_percentage = 0;
            $team->save();
        }

        $fixtures = [];
        $totalWeeks = 6;

        // Generate first round fixtures
        foreach ($teams as $homeTeam) {
            foreach ($teams as $awayTeam) {
                if ($homeTeam->id === $awayTeam->id) {
                    continue;
                }

                // Add both home and away fixtures
                $fixtures[] = [
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id
                ];
            }
        }

        // Shuffle fixtures to randomize the schedule
        shuffle($fixtures);

        // Assign weeks to fixtures ensuring no team plays twice in a week
        $week = 1;
        $usedTeamsInWeek = [];

        foreach ($fixtures as $fixture) {
            $week = 1;
            $homeTeamId = $fixture['home_team_id'];
            $awayTeamId = $fixture['away_team_id'];

            // Check if either team has already played this week
            $weekInLoop = $week;
            while (
                isset($usedTeamsInWeek[$weekInLoop]) && $weekInLoop < $totalWeeks &&
                (in_array($homeTeamId, $usedTeamsInWeek[$weekInLoop]) ||
                    in_array($awayTeamId, $usedTeamsInWeek[$weekInLoop]))
            ) {
                $weekInLoop++;
            }


            // Mark teams as used for this week
            if (!isset($usedTeamsInWeek[$week])) {
                $usedTeamsInWeek[$week] = [];
            }
            $usedTeamsInWeek[$weekInLoop][] = $homeTeamId;
            $usedTeamsInWeek[$weekInLoop][] = $awayTeamId;

            // Create game
            Game::create([
                'home_team_id' => $homeTeamId,
                'away_team_id' => $awayTeamId,
                'week' => $weekInLoop,
                'played' => false,
            ]);


            // Move to next week if current week is full
            if (count($usedTeamsInWeek[$week]) >= $teams->count() / 2 && $week < $totalWeeks) {
                $week++;
            }
        }
    }

    /**
     * Play Gamees for a specific week
     */
    public function playWeek(int $week): void
    {
        $Gamees = Game::where('week', $week)
            ->where('played', false)
            ->get();

        foreach ($Gamees as $Game) {
            $Game->simulate();
        }
    }

    public function getWeekGames(int $week): Collection
    {
        return Game::where('week', $week)
            ->get();
    }
    /**
     * Play all remaining Gamees
     */
    public function playAllGamees(): void
    {
        $Gamees = Game::where('played', false)
            ->orderBy('week')
            ->get();

        foreach ($Gamees as $Game) {
            $Game->simulate();
        }
    }

    /**
     * Get the current week (highest played week + 1 or 1 if no Gamees played)
     */
    public function getCurrentWeek(): int
    {
        $latestPlayedGame = Game::where('played', true)
            ->orderByDesc('week')
            ->first();

        if (!$latestPlayedGame) {
            return 1;
        }

        return $latestPlayedGame->week + 1;
    }

    /**
     * Get total number of weeks in the league
     */
    public function getTotalWeeks(): int
    {
        return Game::max('week') ?? 0;
    }

    /**
     * Check if league is completed
     */
    public function isLeagueCompleted(): bool
    {
        return Game::where('played', false)->count() === 0;
    }

    // Add these functions to your LeagueService class

    /**
     * Calculate winning chances for all teams
     * @return array<int, float> Array of team IDs and their winning chances
     */
    public function calculateLeagueWinningChances(): array
    {
        $teams = Team::all();
        $remainingGames = $this->getRemainingGamesPerTeam();
        $maxPossiblePoints = [];
        $winningChances = [];
        $totalProbability = 0;

        // Calculate max possible points for each team
        foreach ($teams as $team) {
            $maxPossiblePoints[$team->id] = $team->points + ($remainingGames[$team->id] * 3);
        }

        // Get current leader's points
        $leaderPoints = $teams->max('points');

        foreach ($teams as $team) {
            // If team can't mathematically win, chance is 0
            if ($maxPossiblePoints[$team->id] < $leaderPoints) {
                $winningChances[$team->id] = 0;
                continue;
            }

            // Calculate probability based on current points, remaining games and team strength
            $probability = $this->calculateTeamWinningProbability(
                $team,
                $remainingGames[$team->id],
                $maxPossiblePoints[$team->id],
                $leaderPoints
            );

            $winningChances[$team->id] = $probability;
            $totalProbability += $probability;
        }

        // Normalize probabilities to ensure they sum to 100%
        if ($totalProbability > 0) {
            foreach ($winningChances as &$chance) {
                $chance = ($chance / $totalProbability) * 100;
            }
        }
        foreach($teams as $team){
            $team->win_percentage = $winningChances[$team->id];
            $team->save();
        }

        return $winningChances;
    }

    /**
     * Calculate remaining games for each team
     * @return array<int, int>
     */
    private function getRemainingGamesPerTeam(): array
    {
        $teams = Team::all();
        $remainingGames = [];

        foreach ($teams as $team) {
            $totalGames = Game::where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id);
            })->count();

            $playedGames = Game::where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id);
            })->where('played', true)->count();

            $remainingGames[$team->id] = $totalGames - $playedGames;
        }

        return $remainingGames;
    }

    /**
     * Calculate individual team's winning probability
     */
    private function calculateTeamWinningProbability(
        Team $team,
        int $remainingGames,
        int $maxPossiblePoints,
        int $leaderPoints
    ): float {
        // Base probability factors
        $pointsFactor = ($team->points / max(1, $leaderPoints)) * 0.4;
        $strengthFactor = ($team->strength / 10) * 0.3;
        $formFactor = $this->calculateFormFactor($team) * 0.2;
        $remainingGamesFactor = ($remainingGames > 0) ? 0.1 : 0;

        // Combine all factors
        $probability = ($pointsFactor + $strengthFactor + $formFactor + $remainingGamesFactor);

        return $probability;
    }

    /**
     * Calculate team's recent form (based on last 3 games)
     */
    private function calculateFormFactor(Team $team): float
    {
        $recentGames = Game::where(function ($query) use ($team) {
            $query->where('home_team_id', $team->id)
                ->orWhere('away_team_id', $team->id);
        })
            ->where('played', true)
            ->orderBy('week', 'desc')
            ->take(3)
            ->get();

        if ($recentGames->isEmpty()) {
            return 0.5;
        }

        $formPoints = 0;
        foreach ($recentGames as $game) {
            $isHome = $game->home_team_id === $team->id;
            $teamGoals = $isHome ? $game->home_goals : $game->away_goals;
            $opponentGoals = $isHome ? $game->away_goals : $game->home_goals;

            if ($teamGoals > $opponentGoals) {
                $formPoints += 3;
            } elseif ($teamGoals === $opponentGoals) {
                $formPoints += 1;
            }
        }

        return $formPoints / (3 * 3); // Normalize to 0-1 range
    }
}
