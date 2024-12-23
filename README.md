# HubSpot and Housecall Pro Middleware Integration

This project integrates HubSpot and Housecall Pro APIs to synchronize customer data between the two platforms. It provides endpoints to fetch and sync customer records seamlessly using a middleware system built in **Laravel**.

---

## Features

- **Sync Data**: Automatically synchronize customer data between HubSpot and Housecall Pro.
- **Fetch Records**: Retrieve existing records from HubSpot and Housecall Pro APIs.
- **Error Handling**: Detailed error responses for failed integrations.
- **Endpoints Documentation**: Ready-to-use endpoints for integration with tools like Postman.

---

## Prerequisites

- **PHP** >= 7.4
- **Composer** (Dependency manager)
- **Laravel Framework** >= 8.x
- Valid API credentials for:
  - **HubSpot** API
  - **Housecall Pro** API

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/hubspot-housecall-middleware.git
   ```

2. Navigate to the project directory:
   ```bash
   cd hubspot-housecall-middleware
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Create a `.env` file and add your API credentials:
   ```dotenv
   HOUSECALL_PRO_API_URL=https://api.housecallpro.com
   HOUSECALL_PRO_API_KEY=your_housecall_api_key

   HUBSPOT_API_URL=https://api.hubapi.com
   HUBSPOT_API_KEY=your_hubspot_api_key
   ```

5. Run your Laravel development server:
   ```bash
   php artisan serve
   ```

---

## Viewing API Documentation

You can easily access the API documentation in your browser by navigating to the following URL:

[http://127.0.0.1:8000/api/documentation#/](http://127.0.0.1:8000/api/documentation#/)

Once there, you will see an interface like this:

![API Documentation Screenshot](/assets/images/Screenshot_2024-12-23_175204.png)

---

## Endpoints Documentation

Below are the available API endpoints for this integration:

### 1. Fetch HubSpot Customers

- **Method**: `GET`  
- **URL**: `/api/hubspot/customers`  
- **Headers**:
  - `Authorization: Bearer <HUBSPOT_API_KEY>`
  - `Content-Type: application/json`  
- **Response**:
  ```json
  {
    "results": [
      { "id": "123", "email": "example@email.com", "firstname": "John" }
    ]
  }
  ```

---

### 2. Fetch Housecall Pro Customers

- **Method**: `GET`  
- **URL**: `/api/housecall/customers`  
- **Headers**:
  - `Authorization: Bearer <HOUSECALL_PRO_API_KEY>`
  - `Content-Type: application/json`  
- **Response**:
  ```json
  {
    "customers": [
      { "id": "321", "customer_name": "John Doe" }
    ]
  }
  ```

---

### 3. Create HubSpot Customer

- **Method**: `POST`  
- **URL**: `/api/hubspot/customers`  
- **Headers**:
  - `Authorization: Bearer <HUBSPOT_API_KEY>`
  - `Content-Type: application/json`  
- **Body**:
  ```json
  {
    "properties": {
      "email": "jane.doe@example.com",
      "firstname": "Jane",
      "lastname": "Doe"
    }
  }
  ```
- **Response**:
  ```json
  { "id": "124", "properties": { "email": "jane.doe@example.com" } }
  ```

---

### 4. Create Housecall Pro Customer

- **Method**: `POST`  
- **URL**: `/api/housecall/customers`  
- **Headers**:
  - `Authorization: Bearer <HOUSECALL_PRO_API_KEY>`
  - `Content-Type: application/json`  
- **Body**:
  ```json
  {
    "customer_name": "Jane Doe",
    "phone": "987-654-3210",
    "address": "456 Example St"
  }
  ```
- **Response**:
  ```json
  { "id": "322", "customer_name": "Jane Doe" }
  ```

---

### 5. Middleware Sync Data

- **Method**: `POST`  
- **URL**: `/api/sync-data`  
- **Headers**:
  - `Content-Type: application/json`  
- **Body**:
  ```json
  {
    "hubspot_data": {
      "email": "michael.smith@example.com",
      "firstname": "Michael",
      "lastname": "Smith"
    },
    "housecall_pro_data": {
      "customer_name": "Michael Smith",
      "phone": "555-123-4567",
      "address": "789 Sync St"
    }
  }
  ```
- **Response**:
  ```json
  {
    "message": "Data synced successfully.",
    "existing_hubspot_records": { ... },
    "existing_housecall_records": { ... }
  }
  ```

---

## Usage

1. Add the middleware to your routes or controllers in `routes/api.php`:
   ```php
   use App\Http\Middleware\HubSpotHousecallMiddleware;

   Route::middleware([HubSpotHousecallMiddleware::class])->group(function () {
       Route::post('/sync-data', [YourController::class, 'handleSync']);
   });
   ```

2. Test the endpoints using **Postman** or any HTTP client.

---

## Error Handling

- `400`: Missing required data for processing.
- `500`: Failed to sync data to HubSpot or Housecall Pro APIs.
- `200`: Successful responses with detailed integration results.

---

## Notes

- Ensure the API keys are valid and scoped for the required endpoints.
- This middleware requires proper environment variables to function correctly.

---

