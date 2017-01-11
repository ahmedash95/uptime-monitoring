<?php

namespace App\Http\Controllers;

use App\Website;
use Predis\Client;
use Illuminate\Http\Request;
use Predis\Connection\ConnectionException;
use Exception;
use PDO;

class WebsiteController extends Controller
{
    public function index()
    {
    	$websites = Website::all();

    	$sites = [];

    	foreach ($websites as $website) {
    		$site['id'] = $website->id;
            $site['url'] = $website->url;
    		$site['name'] = $website->name;

            switch ($website->type) {
                case 'website':
                    $response = $this->checkWebsite($website->url);
                    if ($response) {
                        $site['status'] = 'online';
                        $site['response'] = $response;
                    } else {
                        $site['status'] = 'down';
                        $site['response'] = 0;
                    }
                    break;

                case 'redis':
                    $response = $this->checkRedis($website->url, $website->password);
                    if ($response) {
                        $site['status'] = 'online';
                        $site['response'] = number_format($response, 3);
                    } else {
                        $site['status'] = 'down';
                        $site['response'] = 0;
                    }
                    break;

                case 'mysql':
                    $response = $this->checkMysql($website->url, $website->username, $website->password, $website->db_name, $website->table_name);
                    if ($response) {
                        $site['status'] = 'online';
                        $site['response'] = number_format($response, 3);
                    } else {
                        $site['status'] = 'down';
                        $site['response'] = 0;
                    }
                    break;
            }

			$sites[] = $site;
    	}
	
	$sites = collect($sites)->sortBy('name');

    	return view('websites.index')->with('sites', $sites);
    }

    public function create()
    {
    	return view('websites.create');
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
            'url' => 'required|unique:websites',
            'name' => 'required',
	        'type' => 'required',
            'username' => 'required_if:type,mysql',
            'password' => 'required_if:type,mysql,redis',
            'db_name' => 'required_if:type,mysql',
            'table_name' => 'required_if:type,mysql',
        ]);

        $data = $request->all();

        $website = Website::create($data);

        return redirect('/websites');
    }

    public function show(Website $website)
    {
        $status = $website->status()->orderBy('created_at', 'dsc')->paginate(10);

        return view('websites.show')->with('website', $website)->with('status', $status);
    }

    public function destroy(Website $website)
    {
    	$website->delete();
    	return redirect('/websites');
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
            return $info['total_time'];
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
