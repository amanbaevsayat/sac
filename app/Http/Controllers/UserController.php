<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Filters\UserFilter;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'users';
        $this->perPage = 45;
    }

    public function getList(UserFilter $filters)
    {
        access(['can-head', 'can-host']);

        $query = User::query();
        $users = $query->latest()->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new UserCollection($users), 200);
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

        return view("{$this->root}.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRequest $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request, User $user)
    {
        access(['can-head', 'can-host']);
        $data = $request->all();
        $data['is_active'] = $request->get('is_active') == 'on' ? true : false;
        $data['password'] = bcrypt($request->get('pass'));
        $user = $user->create($data);
        return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно создан.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        access(['can-head', 'can-host']);

        return view("{$this->root}.show", [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        access(['can-head', 'can-host']);
        return view("{$this->root}.edit", [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserRequest $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        access(['can-head', 'can-host']);
        $data = $request->all();
        $data['is_active'] = $request->get('is_active') == 'on' ? true : false;
        $data['password'] = bcrypt($request->get('pass'));
        $user->update($data);

        $message = 'Данные пользователя успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.edit", [$user->id]))->with('success', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // access(['can-head', 'can-host']);

        // $user->delete();
        // return redirect()->route("{$this->root}.index")->with('success', 'Пользователь успешно удален.');
    }
}
