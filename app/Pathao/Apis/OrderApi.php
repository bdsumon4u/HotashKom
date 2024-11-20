<?php

namespace App\Pathao\Apis;

use App\Pathao\Exceptions\PathaoCourierValidationException;
use App\Pathao\Exceptions\PathaoException;
use GuzzleHttp\Exception\GuzzleException;

class OrderApi extends BaseApi
{
    /**
     * Order Create
     *
     * @param  array  $array
     * @return mixed
     *
     * @throws PathaoException
     * @throws GuzzleException|PathaoCourierValidationException
     */
    public function create($array)
    {
        $this->validation($array, [
            'store_id',
            'recipient_name',
            'recipient_phone',
            'recipient_address',
            'recipient_city',
            'recipient_zone',
            'delivery_type',
            'item_type',
            'item_quantity',
            'amount_to_collect',
        ]);

        $response = $this->authorization()->send('POST', 'aladdin/api/v1/orders', $array);

        return $response->data;
    }

    /**
     * Bulk Create
     *
     * @param  array  $array
     * @return mixed
     *
     * @throws PathaoException
     * @throws GuzzleException|PathaoCourierValidationException
     */
    public function bulk($array)
    {
        foreach ($array as $item) {
            $this->validation($item, [
                'store_id',
                'recipient_name',
                'recipient_phone',
                'recipient_address',
                'recipient_city',
                'recipient_zone',
                'delivery_type',
                'item_type',
                'item_quantity',
                'amount_to_collect',
            ]);
        }

        $response = $this->authorization()->send('POST', 'aladdin/api/v1/orders/bulk', ['orders' => $array]);

        return $response->data;
    }

    /**
     * Order Details
     *
     * @param  string  $consignmentId
     * @return mixed
     *
     * @throws GuzzleException
     * @throws PathaoException
     */
    public function orderDetails($consignmentId)
    {
        $response = $this->authorization()->send('GET', "aladdin/api/v1/orders/{$consignmentId}");

        return $response->data;
    }

    /**
     * Delivery price calculation
     *
     * @param  array  $array
     * @return mixed
     *
     * @throws GuzzleException
     * @throws PathaoException|PathaoCourierValidationException
     */
    public function priceCalculation($array)
    {
        $this->validation($array, [
            'store_id',
            'item_type',
            'delivery_type',
            'item_weight',
            'recipient_city',
            'recipient_zone',
        ]);

        $response = $this->authorization()->send('POST', 'aladdin/api/v1/merchant/price-plan', $array);

        return $response->data;
    }
}
