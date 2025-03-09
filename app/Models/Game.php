<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_goals',
        'away_goals',
        'week',
        'played',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }


    public function simulate(): void
    {
        if ($this->played) {
            return;
        }

        $homeAdvantage = 1.3; // Home advantage multiplier
        
        // Calculate expected goals based on team strengths
        $expectedHomeGoals = ($this->homeTeam->strength / 10) * $homeAdvantage * 2.5;
        $expectedAwayGoals = ($this->awayTeam->strength / 10) * 1.8;
        
        // Simulate actual goals using Poisson distribution
        $this->home_goals = $this->poissonRandom($expectedHomeGoals);
        $this->away_goals = $this->poissonRandom($expectedAwayGoals);
        $this->played = true;
        $this->save();

        // Update team stats
        $this->homeTeam->updateStats($this->home_goals, $this->away_goals, true);
        $this->awayTeam->updateStats($this->away_goals, $this->home_goals, false);
    }

    /**
     * Update match result and recalculate team stats.
     */
    public function updateResult(int $homeGoals, int $awayGoals): void
    {
        if (!$this->played) {
            return;
        }

        // Revert previous stats
        $this->homeTeam->played -= 1;
        $this->homeTeam->goals_for -= $this->home_goals;
        $this->homeTeam->goals_against -= $this->away_goals;
        $this->awayTeam->played -= 1;
        $this->awayTeam->goals_for -= $this->away_goals;
        $this->awayTeam->goals_against -= $this->home_goals;

        // Revert win/draw/loss stats
        if ($this->home_goals > $this->away_goals) {
            $this->homeTeam->won -= 1;
            $this->homeTeam->points -= 3;
            $this->awayTeam->lost -= 1;
        } elseif ($this->home_goals === $this->away_goals) {
            $this->homeTeam->drawn -= 1;
            $this->homeTeam->points -= 1;
            $this->awayTeam->drawn -= 1;
            $this->awayTeam->points -= 1;
        } else {
            $this->homeTeam->lost -= 1;
            $this->awayTeam->won -= 1;
            $this->awayTeam->points -= 3;
        }

        // Save new result
        $this->home_goals = $homeGoals;
        $this->away_goals = $awayGoals;
        $this->save();

        // Apply new stats
        $this->homeTeam->updateStats($homeGoals, $awayGoals, true);
        $this->awayTeam->updateStats($awayGoals, $homeGoals, false);
    }

    /**
     * Poisson random number generator
     */
    private function poissonRandom(float $lambda): int
    {
        $l = exp(-$lambda);
        $k = 0;
        $p = 1.0;
        
        do {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $l);
        
        return $k - 1;
    }
}
