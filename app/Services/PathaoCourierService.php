<?php

namespace App\Services;

use App\Models\Courier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PathaoCourierService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;

    public function __construct(
        Courier $courier,
        string $clientId     = null,
        string $clientSecret = null,
        string $username     = null,
        string $password     = null,
        string $baseUrl      = null
    ) {
        $this->baseUrl      = $courier->base_url ?? $baseUrl      ?? config('pathao.base_url');
        $this->clientId     = $courier->api_key ?? $clientId     ?? config('pathao.client_id');
        $this->clientSecret = $courier->secret_key  ?? $clientSecret ?? config('pathao.client_secret');
        $this->username     = $courier->username  ?? $username     ?? config('pathao.username');
        $this->password     = $courier->password  ?? $password     ?? config('pathao.password');
    }

    // Override credentials at runtime (SaaS / multi-tenant)
    public static function withConfig(
        string $clientId,
        string $clientSecret,
        string $username,
        string $password,
        string $baseUrl = null
    ): static {
        return new static($clientId, $clientSecret, $username, $password, $baseUrl);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Access Token (auto-cached)
    |--------------------------------------------------------------------------
    */
    protected function getAccessToken(): string
    {
        $cacheKey = 'pathao_access_token_' . md5(
            $this->clientId . '_' . $this->username
        );

        return Cache::remember($cacheKey, now()->addDays(5), function () {

            $response = $this->issueToken();

            if (!isset($response['access_token'])) {
                throw new \Exception('Pathao token generation failed');
            }

            return $response['access_token'];
        });
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Issue Access Token
    |--------------------------------------------------------------------------
    */
    public function issueToken(): array
    {
        return Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'password',
            'username'      => $this->username,
            'password'      => $this->password,
        ])->throw()
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Access Token
    |--------------------------------------------------------------------------
    */
    public function refreshToken(string $refreshToken): array
    {
        return Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ])->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Create a New Store
    |--------------------------------------------------------------------------
    */
    public function createStore(array $data): array
    {
        return Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/aladdin/api/v1/stores', $data)
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Merchant Stores
    |--------------------------------------------------------------------------
    */
    public function getStores(): array
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/aladdin/api/v1/stores')
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Place Single Order
    |--------------------------------------------------------------------------
    */
    public function placeOrder(array $data): array
    {
        return Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/aladdin/api/v1/orders', $data)
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Order Creation
    |--------------------------------------------------------------------------
    */
    public function bulkCreateOrders(array $orders): array
    {
        return Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/aladdin/api/v1/orders/bulk', ['orders' => $orders])
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Order Short Info by Consignment ID
    |--------------------------------------------------------------------------
    */
    public function getOrderInfo(string $consignmentId): array
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/aladdin/api/v1/orders/' . $consignmentId . '/info')
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get City List
    |--------------------------------------------------------------------------
    */
    public function getCities(): array
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/aladdin/api/v1/city-list')
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Zones by City ID
    |--------------------------------------------------------------------------
    */
    public function getZones(int $cityId): array
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/aladdin/api/v1/cities/' . $cityId . '/zone-list')
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Areas by Zone ID
    |--------------------------------------------------------------------------
    */
    public function getAreas(int $zoneId): array
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/aladdin/api/v1/zones/' . $zoneId . '/area-list')
            ->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Price Calculation
    |--------------------------------------------------------------------------
    */
    public function calculatePrice(array $data): array
    {
        return Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/aladdin/api/v1/merchant/price-plan', $data)
            ->json();
    }
}
