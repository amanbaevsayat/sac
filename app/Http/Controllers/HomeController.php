<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    private $githash;

    function __construct()
    {
        $this->githash = env('GIT_HASH');
    }

    public function homepage()
    {
        return view('home');
    }

    public function thankYou()
    {
        return view('thank-you');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function pull(Request $request)
    {
        $data = $request->validate([
            'githash' => 'required|string',
            'branch' => 'required|string|in:staging,master',
        ]);

        if ($data["githash"] != $this->githash) {
            return response()->with(["error" => "Hash is invalid"]);
        }

        $branch = $data["branch"];

        Artisan::call("git:pull --branch={$branch}");

        return response()->json([
            'status' => true
        ]);
    }
}
