<?php

namespace App\Pathao\Apis;

use App\Pathao\Exceptions\PathaoCourierValidationException;
use App\Pathao\Exceptions\PathaoException;
use App\Repositories\SettingRepository;
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
            'timeout' => 5, // 5 second timeout
            'connect_timeout' => 3, // 3 second connection timeout
        ]);
    }

    /**
     * Set Base Url on sandbox mode
     */
    private function setBaseUrl(): void
    {
        if (config('pathao.sandbox') == true) {
            $this->baseUrl = 'https://hermes-api.p-stageenv.xyz';
        } else {
            $this->baseUrl = 'https://api-hermes.pathao.com';
        }
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
     * set authentication token
     *
     * @throws PathaoException|GuzzleException
     */
    private function authenticate(): void
    {
        try {
            $Pathao = optional(app(SettingRepository::class)->first('Pathao')->value);
            $response = $this->send('POST', 'aladdin/api/v1/issue-token', [
                'client_id' => $Pathao->client_id,
                'client_secret' => $Pathao->client_secret,
                'username' => $Pathao->username,
                'password' => $Pathao->password,
                'grant_type' => 'password',
            ]);

            file_put_contents(storage_path('app/pathao_bearer_token.json'), json_encode([
                'token' => 'Bearer '.$response->access_token,
                'expires_in' => time() + $response->expires_in,
            ]));

        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
            throw new PathaoException($response->message, $response->code);
        }
    }

    /**
     * Authorization set to header
     *
     * @return $this
     *
     * @throws PathaoException|GuzzleException
     */
    public function authorization()
    {
        $storageExits = file_exists(storage_path('app/pathao_bearer_token.json'));

        if (! $storageExits) {
            $this->authenticate();
        }

        $jsonToken = file_get_contents(storage_path('app/pathao_bearer_token.json'));
        $jsonToken = json_decode($jsonToken);

        if ($jsonToken->expires_in < time()) {
            $this->authenticate();
            $jsonToken = file_get_contents(storage_path('app/pathao_bearer_token.json'));
            $jsonToken = json_decode($jsonToken);
        }

        $this->mergeHeader([
            'Authorization' => $jsonToken->token,
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
     * @throws PathaoException
     */
    public function send($method, $uri, $body = [])
    {
        try {
            $response = $this->request->request($method, $uri, [
                'headers' => $this->headers,
                'body' => json_encode($body),
            ]);

            return json_decode($response->getBody());
        } catch (ClientException $e) {
            if ($e->getCode() == 401) {
                $message = 'Unauthorized';
                $errors = [];
            } else {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $message = $response->message;
                $errors = $response->errors ?? [];
            }
            throw new PathaoException($message, $e->getCode(), $errors);
        }
    }

    /**
     * Data Validation
     *
     * @param  array  $data
     * @param  array  $requiredFields
     *
     * @throws PathaoCourierValidationException
     */
    public function validation($data, $requiredFields): void
    {
        throw_if(! is_array($data) || ! is_array($requiredFields), \TypeError::class, 'Argument must be of the type array', 500);

        throw_if(! count($data) || ! count($requiredFields), PathaoCourierValidationException::class, 'Invalid data!', 422);

        $requiredColumns = array_diff($requiredFields, array_keys($data));
        throw_if(count($requiredColumns), PathaoCourierValidationException::class, $requiredColumns, 422);

        foreach ($requiredFields as $filed) {
            throw_if(isset($data[$filed]) && empty($data[$filed]), PathaoCourierValidationException::class, "$filed is required", 422);
        }

    }
}
