<?php

namespace App\Console\Commands\Git;

use Illuminate\Console\Command;

class GitPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push git changes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
