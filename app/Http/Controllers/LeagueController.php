<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Http\Resources\TeamResource;
use App\Models\Estimation;
use App\Models\Game;
use App\Models\Team;
use App\Services\LeagueService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeagueController extends Controller
{
    protected LeagueService $leagueService;

    public function __construct(LeagueService $leagueService)
    {
        $this->leagueService = $leagueService;
    }

    public function index()
    {
        
        if (Team::count() === 0) {
            Team::create(['name' => 'Arsenal', 'strength' => 10]);
            Team::create(['name' => 'Manchester City', 'strength' => 6]);
            Team::create(['name' => 'Liverpool', 'strength' => 8]);
            Team::create(['name' => 'Chelsea', 'strength' => 7]);
        }

        $teams = Team::orderByDesc('points')->orderByDesc('goals_for', 'desc')->get();

        return Inertia::render('League/Index', [
            'teams' => TeamResource::collection($teams),

        ]);
    }

    public function scheduledMatches()
    {
        // $games = $this->leagueService->getGameesByWeek();
        $games = Game::query()->orderBy('week')->orderBy('id')->get();
        return Inertia::render('League/ScheduledMatches', [
            'matches' => GameResource::collection($games)->groupBy('week'),
            'weeks' => $this->leagueService->getTotalWeeks(),
            'currentWeek' => $this->leagueService->getCurrentWeek(),
            'isCompleted' => $this->leagueService->isLeagueCompleted(),
            'estimations' => Estimation::all(),
        ]);
    }


    public function initialize()
    {
       
        $this->leagueService->scheduleGamees();

        return redirect()->route('league.scheduled-matches');
    }

    public function startSimulation()
    {
        $this->leagueService->calculateLeagueWinningChances();
        $currentWeek = $this->leagueService->getCurrentWeek();
        $games = Game::query()->where('played', 1)->orderBy('week')->orderBy('id')->get();
        $teams = Team::orderByDesc('points')->orderByDesc('goals_for', 'desc')->get();
        
        $thisWeekMatches = $this->leagueService->getWeekGames($currentWeek);
       
        return Inertia::render('League/Simulation', [
            'teams' => TeamResource::collection($teams),
            'matches' => GameResource::collection($games)->groupBy('week'),
            'weeks' => $this->leagueService->getTotalWeeks(),
            'currentWeek' => $currentWeek,
            'isCompleted' => $this->leagueService->isLeagueCompleted(),
            'estimations' => Estimation::all(),
            'weekMatches' => GameResource::collection($thisWeekMatches),
        ]);
    }


    public function playNextWeek()
    {
        $currentWeek = $this->leagueService->getCurrentWeek();
        $this->leagueService->playWeek($currentWeek);

        return redirect()->route('league.start-simulation');
    }


    public function playAllGamees()
    {
        $this->leagueService->playAllGamees();

        return redirect()->route('league.start-simulation');
    }

    public function updateGame(Request $request, Game $Game)
    {
        $request->validate([
            'home_goals' => 'required|integer|min:0',
            'away_goals' => 'required|integer|min:0',
        ]);

        $Game->updateResult(
            $request->input('home_goals'),
            $request->input('away_goals')
        );

        return redirect()->route('league.start-simulation');
    }

    /**
     * Submit estimation
     */
    public function submitEstimation(Request $request)
    {
        $request->validate([
            'estimations' => 'required|array',
            'estimations.*.team_id' => 'required|exists:teams,id',
            'estimations.*.position' => 'required|integer|min:1',
            'estimations.*.predicted_points' => 'required|integer|min:0',
        ]);

        // Delete existing estimations
        Estimation::truncate();

        // Save new estimations
        foreach ($request->input('estimations') as $estimation) {
            Estimation::create([
                'team_id' => $estimation['team_id'],
                'position' => $estimation['position'],
                'predicted_points' => $estimation['predicted_points'],
            ]);
        }

        return redirect()->route('league.index');
    }
}
