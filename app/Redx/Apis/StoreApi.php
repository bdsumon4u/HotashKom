<?php

namespace App\Redx\Apis;

use App\Redx\Exceptions\RedxException;
use GuzzleHttp\Exception\GuzzleException;

class StoreApi extends BaseApi
{
    /**
     *  Get Store List
     *
     * @return mixed
     *
     * @throws GuzzleException
     * @throws RedxException
     */
    public function list()
    {
        $response = $this->authorization()->send('GET', 'v1.0.0-beta/pickup/stores');

        return $response;
    }

    /**
     * Get store details
     *
     *
     * @return mixed
     *
     * @throws GuzzleException
     * @throws RedxException
     */
    public function storeDetails($storeId)
    {
        $response = $this->authorization()->send('GET', "v1.0.0-beta/pickup/store/info/{$storeId}");

        return $response->pickup_store;
    }

    /**
     * Store Create
     *
     * @param  array  $storeInfo
     * @return mixed
     *
     * @throws GuzzleException
     * @throws RedxException
     */
    public function create($storeInfo)
    {
        $response = $this->authorization()->send('POST', 'v1.0.0-beta/pickup/store', $storeInfo);

        return $response;
    }
}
