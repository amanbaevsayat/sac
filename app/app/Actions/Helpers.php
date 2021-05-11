<?php

use App\Services\GitService;
use Illuminate\Support\Facades\Gate;

function version()
{
    return resolve(GitService::class)->version();
}

function access(array $gates)
{
    if (Gate::none($gates)) {
        abort(403);
    }
}
