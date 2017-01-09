<?php

namespace App\Http\Controllers;

use App\Website;
use Predis\Client;
use Illuminate\Http\Request;
use Predis\Connection\ConnectionException;
use Exception;

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

            if ($website->type == 'website') {
                $response = $this->checkWebsite($website->url);
                if ($response) {
                    $site['status'] = 'online';
                    $site['response'] = $response;
                } else {
                    $site['status'] = 'down';
                    $site['response'] = 0;
                }
                
            } else {
                $response = $this->checkRedis($website->url);
                if ($response) {
                    $site['status'] = 'online';
                    $site['response'] = 0;
                } else {
                    $site['status'] = 'down';
                    $site['response'] = 0;
                }
            }
			$sites[] = $site;
    	}

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
        ]);

        $website = Website::create([
            'url' => $request->input('url'),
            'name' => $request->input('name'),
        	'type' => $request->input('type')
        ]);

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
