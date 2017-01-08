<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class RequestsMonitoring extends Controller
{
    public function index(){
    	return view('requests.index');
    }
    public function store(){
    	
    }

    public function home()
    {
    	$healthyMonitor = MonitorRepository::getHealthy();

        if (! $healthyMonitor->count()) {
            return;
        }

    	$rows = $healthyMonitor->map(function (Monitor $monitor) {
            $url = $monitor->getUrlAttribute();
            $url = $url->getHost();

            $reachable = $monitor->uptimeStatusAsEmoji;

            $onlineSince = $monitor->formattedLastUpdatedStatusChangeDate('forHumans');

            if ($monitor->certificate_check_enabled) {
                $certificateFound = $monitor->certificateStatusAsEmoji;
                $certificateExpirationDate = $monitor->formattedCertificateExpirationDate('forHumans');
                $certificateIssuer = $monitor->certificate_issuer;
            }

            return compact('url', 'reachable', 'onlineSince', 'certificateFound', 'certificateExpirationDate', 'certificateIssuer');
        });

        $titles = ['URL', 'Uptime check', 'Online since', 'Certificate check', 'Certificate Expiration date', 'Certificate Issuer'];

        return view('index')->with('titles', $titles)->with('rows', $rows);
    }
}
