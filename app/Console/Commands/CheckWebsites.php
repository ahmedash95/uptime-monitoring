<?php

namespace App\Console\Commands;

use App\User;
use App\Website;
use Illuminate\Console\Command;
use App\Notifications\ServerDown;

class CheckWebsites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:websites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $websites = Website::all();

        foreach ($websites as $website) {
            $site['id'] = $website->id;
            $site['url'] = $website->url;
            $site['name'] = $website->name;
            $ch = curl_init($website->url); 
            curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT,10);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpcode == 200)
            {
            $info = curl_getinfo($ch);
                $site['status'] = 'online';
                $site['response_time'] = $info['total_time'];
            } else {
                $site['status'] = 'down';
                $site['response'] = 0;

                $user = User::first();
                $user->notify(new ServerDown($site['name']));
            }
            curl_close($ch);

            $website->status()->create($site);
        }
    }
}
