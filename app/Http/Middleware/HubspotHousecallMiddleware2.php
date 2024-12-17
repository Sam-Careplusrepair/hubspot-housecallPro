<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HubspotHousecallMiddleware2
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request has required data for processing
        if (!$request->has(['hubspot_data', 'housecall_pro_data'])) {
            return response()->json(['error' => 'Missing data for integration.'], 400);
        }

        // Extract data from the request
        $hubspotData = $request->input('hubspot_data');
        $housecallData = $request->input('housecall_pro_data');

        // Fetch all existing records from HubSpot and Housecall Pro
        $existingHubSpotRecords = $this->fetchHubSpotRecords();
        $existingHousecallRecords = $this->fetchHousecallRecords();

        // Sync data from HubSpot to Housecall Pro
        $housecallResponse = $this->syncToHousecallPro($hubspotData);

        if ($housecallResponse->failed()) {
            return response()->json(['error' => 'Failed to sync to Housecall Pro'], 500);
        }

        // Sync data from Housecall Pro to HubSpot
        $hubspotResponse = $this->syncToHubSpot($housecallData);

        if ($hubspotResponse->failed()) {
            return response()->json(['error' => 'Failed to sync to HubSpot'], 500);
        }

        // Return the fetched and synced data as a response
        return response()->json([
            'message' => 'Data synced successfully.',
            'existing_hubspot_records' => $existingHubSpotRecords,
            'existing_housecall_records' => $existingHousecallRecords
        ]);
    }

    /**
     * Sync data to Housecall Pro API.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function syncToHousecallPro(array $data)
    {
        $housecallProApiUrl = env('HOUSECALL_PRO_API_URL');
        $housecallProApiKey = env('HOUSECALL_PRO_API_KEY');

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $housecallProApiKey,
            'Content-Type' => 'application/json',
        ])->post($housecallProApiUrl, $data);
    }

    /**
     * Sync data to HubSpot API.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function syncToHubSpot(array $data)
    {
        $hubspotApiUrl = env('HUBSPOT_API_URL');
        $hubspotApiKey = env('HUBSPOT_API_KEY');

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $hubspotApiKey,
            'Content-Type' => 'application/json',
        ])->post($hubspotApiUrl, $data);
    }

    /**
     * Fetch all existing records from HubSpot API.
     *
     * @return array
     */
    protected function fetchHubSpotRecords()
    {
        $hubspotApiUrl = env('HUBSPOT_API_URL');
        $hubspotApiKey = env('HUBSPOT_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $hubspotApiKey,
            'Content-Type' => 'application/json',
        ])->get($hubspotApiUrl . '/contacts');

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => 'Failed to fetch HubSpot records'];
    }

    /**
     * Fetch all existing records from Housecall Pro API.
     *
     * @return array
     */
    protected function fetchHousecallRecords()
    {
        $housecallProApiUrl = env('HOUSECALL_PRO_API_URL');
        $housecallProApiKey = env('HOUSECALL_PRO_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $housecallProApiKey,
            'Content-Type' => 'application/json',
        ])->get($housecallProApiUrl . '/customers');

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => 'Failed to fetch Housecall Pro records'];
    }
}


/**
 * POSTMAN API DOCUMENTATION
 * 
 * 1. Fetch HubSpot Customers
 *    - Method: GET
 *    - URL: {{base_url}}/api/hubspot/customers
 *    - Headers: 
 *        - Authorization: Bearer <HUBSPOT_API_KEY>
 *        - Content-Type: application/json
 *    - Response:
 *        {
 *           "results": [{ "id": "123", "email": "example@email.com", "firstname": "John" }]
 *        }
 * 
 * 2. Fetch Housecall Pro Customers
 *    - Method: GET
 *    - URL: {{base_url}}/api/housecall/customers
 *    - Headers: 
 *        - Authorization: Bearer <HOUSECALL_PRO_API_KEY>
 *        - Content-Type: application/json
 *    - Response:
 *        {
 *           "customers": [{ "id": "321", "customer_name": "John Doe" }]
 *        }
 * 
 * 3. Create HubSpot Customer
 *    - Method: POST
 *    - URL: {{base_url}}/api/hubspot/customers
 *    - Headers: 
 *        - Authorization: Bearer <HUBSPOT_API_KEY>
 *        - Content-Type: application/json
 *    - Body:
 *        {
 *           "properties": {
 *               "email": "jane.doe@example.com",
 *               "firstname": "Jane",
 *               "lastname": "Doe"
 *           }
 *        }
 *    - Response:
 *        { "id": "124", "properties": { "email": "jane.doe@example.com" } }
 *
 * 4. Create Housecall Pro Customer
 *    - Method: POST
 *    - URL: {{base_url}}/api/housecall/customers
 *    - Headers: 
 *        - Authorization: Bearer <HOUSECALL_PRO_API_KEY>
 *        - Content-Type: application/json
 *    - Body:
 *        {
 *           "customer_name": "Jane Doe",
 *           "phone": "987-654-3210",
 *           "address": "456 Example St"
 *        }
 *    - Response:
 *        { "id": "322", "customer_name": "Jane Doe" }
 *
 * 5. Middleware Sync Data
 *    - Method: POST
 *    - URL: {{base_url}}/api/sync-data
 *    - Headers: 
 *        - Content-Type: application/json
 *    - Body:
 *        {
 *           "hubspot_data": {
 *               "email": "michael.smith@example.com",
 *               "firstname": "Michael",
 *               "lastname": "Smith"
 *           },
 *           "housecall_pro_data": {
 *               "customer_name": "Michael Smith",
 *               "phone": "555-123-4567",
 *               "address": "789 Sync St"
 *           }
 *        }
 *    - Response:
 *        {
 *           "message": "Data synced successfully.",
 *           "existing_hubspot_records": {...},
 *           "existing_housecall_records": {...}
 *        }
 */

?>
