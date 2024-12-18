<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\HubspotHousecallMiddleware2;

class HubSpotController extends Controller
{
    
    public function getHubSpotContacts()
    {
        $middleware = new HubSpotHousecallMiddleware2();
        $records = $middleware->getHubSpotContacts();

        return response()->json($records);
    }
}
