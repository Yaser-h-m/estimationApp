<?php

namespace App\Models;

use App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'strength',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'points',
        'win_percentage'
    ];


    public function homeGames(): HasMany
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function awayGames(): HasMany
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    public function getGoalDifferenceAttribute(): int
    {
        return $this->goals_for - $this->goals_against;
    }

    public function updateStats(int $goalsFor, int $goalsAgainst, bool $isHome = true): void
    {
        $this->played += 1;
        $this->goals_for += $goalsFor;
        $this->goals_against += $goalsAgainst;

        if ($goalsFor > $goalsAgainst) {
            $this->won += 1;
            $this->points += 3;
        } elseif ($goalsFor === $goalsAgainst) {
            $this->drawn += 1;
            $this->points += 1;
        } else {
            $this->lost += 1;
        }

        $this->save();
    }
}
