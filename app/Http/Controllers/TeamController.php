<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Filters\TeamFilter;
use App\Http\Requests\TeamRequest;
use App\Http\Resources\ProductUsersResource;
use App\Http\Resources\TeamCollection;
use App\Models\User;
use Carbon\Carbon;

class TeamController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'teams';
        $this->perPage = 45;
    }

    public function getList(TeamFilter $filters)
    {
        access(['can-head', 'can-host']);

        $query = Team::query();
        $teams = $query->latest()->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new TeamCollection($teams), 200);
    }

    public function getFilters()
    {
        access(['can-head', 'can-host']);

        $data['main'] = [];

        return response()->json($data, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        access(['can-head', 'can-host']);

        return view("{$this->root}.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        access(['can-head', 'can-host']);
        $users = User::all()->pluck('account', 'id')->toArray();

        return view("{$this->root}.create", [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TeamRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TeamRequest $request, Team $team)
    {
        access(['can-head', 'can-host']);
        $this->updateOrCreate($request->all(), $team, 'create');

        return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно создан.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        access(['can-head', 'can-host']);

        return view("{$this->root}.show", [
            'team' => $team,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        access(['can-head', 'can-host']);
        $teamUsers = $team->users;
        $users = User::all()->pluck('account', 'id')->toArray();

        return view("{$this->root}.edit", [
            'team' => $team,
            'teamUsers' => ProductUsersResource::collection($teamUsers),
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TeamRequest $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(TeamRequest $request, Team $team)
    {
        access(['can-head', 'can-host']);
        $this->updateOrCreate($request->all(), $team, 'update');

        $message = 'Данные продукта успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.edit", [$team->id]))->with('success', $message);
        }
    }

    private function updateOrCreate(array $request, ?Team $team, $type) 
    {
        $teamUsers = $request['productUsers'] ?? [];

        if ($type == 'update') {
            $team->update($request);
        } else if ($type == 'create') {
            $team = $team->create($request);
        }

        $team->users()->detach();

        foreach ($teamUsers as $teamUser) {
            $team->users()->attach([
                $teamUser['id'] => [
                    'stake' => $teamUser['stake'],
                    'employment_at' => Carbon::parse($teamUser['employment_at']),
                ],
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        // access(['can-head', 'can-host']);

        // $team->delete();
        // return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно удален.');
    }
}
