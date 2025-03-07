<?php

namespace App\Redx\Apis;

use App\Redx\Exceptions\RedxException;
use GuzzleHttp\Exception\GuzzleException;

class AreaApi extends BaseApi
{
    /**
     * get city List
     *
     * @return mixed
     *
     * @throws RedxException
     * @throws GuzzleException
     */
    public function list()
    {
        return $this->authorization()->send('GET', 'v1.0.0-beta/areas');
    }
}
