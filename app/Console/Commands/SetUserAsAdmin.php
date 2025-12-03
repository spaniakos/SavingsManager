<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetUserAsAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-admin {email : The email address of the user to set as admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a user as admin by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = \App\Models\User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");

            return 1;
        }

        $user->is_admin = true;
        $user->save();

        $this->info("User '{$user->name}' ({$email}) has been set as admin.");

        return 0;
    }
}
