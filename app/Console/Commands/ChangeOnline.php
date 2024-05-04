<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-online';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::all();
        foreach ($users as $user)
        {
            if($user->last_seen !== Carbon::now()->format('Y-m-d'))
            {
                $user->is_online = 0;
                $user->save();
            }
        }
    }
}
