<?php

use App\Services\GitService;

function version()
{
    return resolve(GitService::class)->version();
}
