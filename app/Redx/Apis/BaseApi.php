<?php

namespace App\Redx\Apis;

use App\Redx\Exceptions\RedxException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class BaseApi
{
    /**
     * @var string
     */
    private $baseUrl;

    private readonly \GuzzleHttp\Client $request;

    /**
     * @var array
     */
    private $headers;

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setHeaders();
        $this->request = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => $this->headers,
        ]);
    }

    /**
     * Set Base Url on sandbox mode
     */
    private function setBaseUrl(): void
    {
        $this->baseUrl = config('redx.sandbox') == true ? 'https://sandbox.redx.com.bd' : 'https://openapi.redx.com.bd';
    }

    /**
     * Set Default Headers
     */
    private function setHeaders(): void
    {
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Merge Headers
     *
     * @param  array  $header
     */
    private function mergeHeader($header): void
    {
        $this->headers = array_merge($this->headers, $header);
    }

    /**
     * Authorization set to header
     *
     * @return $this
     */
    public function authorization()
    {
        $this->mergeHeader([
            'API-ACCESS-TOKEN' => 'Bearer '.config('redx.access_token'),
        ]);

        return $this;
    }

    /**
     * Sending Request
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $body
     * @return mixed
     *
     * @throws GuzzleException
     * @throws RedxException
     */
    public function send($method, $uri, $body = [])
    {
        info(json_encode($body));
        try {
            $response = $this->request->request($method, $uri, [
                'headers' => $this->headers,
                'body' => json_encode($body),
            ]);

            return json_decode($response->getBody());
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
            $message = $response->message;
            $errors = $response->errors ?? [];
            throw new RedxException($message, $e->getCode(), $errors);
        }
    }
}
