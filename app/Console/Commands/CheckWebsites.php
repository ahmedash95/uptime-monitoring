<?php

namespace App\Console\Commands;

use App\User;
use Exception;
use App\Website;
use Predis\Client;
use Illuminate\Console\Command;
use App\Notifications\ServerDown;
use Predis\Connection\ConnectionException;

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

            if ($website->type == 'website') {
                $response = $this->checkWebsite($website->url);
                if ($response) {
                    $site['status'] = 'online';
                    $site['response'] = $response;
                } else {
                    $site['status'] = 'down';
                    $site['response'] = 0;

                    $user = User::first();
                    $user->notify(new ServerDown($site['name']));
                }
                
            } else {
                $response = $this->checkRedis($website->url);
                if ($response) {
                    $site['status'] = 'online';
                    $site['response'] = 0;
                } else {
                    $site['status'] = 'down';
                    $site['response'] = 0;

                    $user = User::first();
                    $user->notify(new ServerDown($site['name']));
                }
            }

            $website->status()->create($site);
        }
    }

        public function checkRedis($url)
    {
        try {
            // $starttime = microtime(true);

            $client = new Client($url);
            $client->set('foo', 'bar');
            $value = $client->get('foo');
            return true;

            // $endtime = microtime(true);
            // $timediff = $endtime - $starttime;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkWebsite($url)
    {
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode == 200)
        {
            return $info['total_time'];
        } else {
            return false;
        }
        
    }
}
