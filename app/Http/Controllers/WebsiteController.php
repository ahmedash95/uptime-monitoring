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
    		$ch = curl_init($website->url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			if(curl_exec($ch))
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
	        'url' => 'required|unique:websites|url',
        ]);

        $website = Website::create([
        	'url' => $request->input('url')
        ]);

        return redirect('/websites');
    }

    public function destroy(Website $website)
    {
    	$website->delete();
    	return redirect('/websites');
    }

}