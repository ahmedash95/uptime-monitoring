<?php

namespace App\Console\Commands;

use PDO;
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

            switch ($website->type) {
                case 'website':
                    $response = $this->checkWebsite($website->url);
                    if ($response[0]) {
                        $site['status'] = 'online';
                        $site['response_time'] = $response[0];
                    } else {
                        $site['status'] = 'down';
                        $site['response_time'] = 0;
                        $site['response'] = $response[1];

                        $user = User::first();
                        $user->notify(new ServerDown($site['name'],$site['url'] ,$site['response'] = $response[1]));
                    }
                    break;

                case 'redis':
                    $response = $this->checkRedis($website->url, $website->password);
                    if ($response) {
                        $site['status'] = 'online';
                        $site['response_time'] = number_format($response, 3);
                    } else {
                        $site['status'] = 'down';
                        $site['response_time'] = 0;

                        $user = User::first();
                        $user->notify(new ServerDown($site['name']));
                    }
                    break;

                case 'mysql':
                    $response = $this->checkMysql($website->url, $website->username, $website->password, $website->db_name, $website->table_name);
                    if ($response) {
                        $site['status'] = 'online';
                        $site['response_time'] = number_format($response, 3);
                    } else {
                        $site['status'] = 'down';
                        $site['response_time'] = 0;

                        $user = User::first();
                        $user->notify(new ServerDown($site['name']));
                    }
                    break;
            }

            $website->status()->create($site);
        }
    }

    public function checkRedis($url, $password)
    {
        try {
            $starttime = microtime(true);

            $client = new Client($url .'?Auth='. $password);
            $client->set('foo', 'bar');
            $value = $client->get('foo');

            $endtime = microtime(true);
            $timediff = $endtime - $starttime;
            return $timediff * 1000;
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
            return [$info['total_time'], $output];
        } else {
            return false;
        }
        
    }

    public function checkMysql($servername, $username, $password, $db_name, $table_name)
    {
        try {
                $time = microtime(true);
                $conn = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare("SELECT id FROM $table_name limit 1"); 
                $stmt->execute();

                // set the resulting array to associative
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $diff = microtime(true)-$time;
                $milliseconds =  $diff * 1000;
                return $milliseconds;
            }
        catch(PDOException $e)
            {
                die($e->getMessage());
                return false;
            }
    }
}
