<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'home_team' => $this->homeTeam->name,
            'away_team' => $this->awayTeam->name,
            'home_goals' => $this->home_goals,
            'away_goals' => $this->away_goals,
            'week' => $this->week,
            'played' => $this->played,
            
        ];
    }
}
