<?php

namespace App\Http\Controllers;

use App\Website;
use Illuminate\Http\Request;

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
				$site['response'] = $info['total_time'];
			} else {
				$site['status'] = 'down';
				$site['response'] = 0;
			}
			curl_close($ch);
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
        ]);

        $website = Website::create([
            'url' => $request->input('url'),
        	'name' => $request->input('name')
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

}
