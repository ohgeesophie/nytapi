<?php

namespace App\Services;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Rules\Offset;
use App\Rules\ISBNLength;


class NYTAPIService
{
    const TIMEOUT = 10;
    const LIST_GLUE = ';';

    protected $validated;
    protected $api_key;
    protected $api_url;

    /**
     * Constructs a new remote api service object.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct()
    {
        $this->validated = [];
        $this->api_key = config('remoteapi.nyt_api_key');
        $this->api_url = config('remoteapi.nyt_api_url_bestsellers');
    }

    /**
     * Fetch data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fetch(Request $request)
    {
        $validData = $this->validate($request);
        $payload = $this->makePayload($validData);
        $response = $this->fetchRemoteJson($payload);

        return $response;
    }

    /**
     * Validate request data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function validate(Request $request)
    {
        $this->validated = $request->validate([
            'author' => 'nullable|string',
            'isbn' => 'nullable|array',
            'isbn.*' => ['nullable', 'bail', 'string', new ISBNLength],
            'title' => 'nullable|string',
            'offset' => ['nullable', 'bail', 'integer', new Offset],
        ]);

        return $this->validated;
    }

    /**
     * Generate data payload for remote request
     *
     * @param array
     * @return array
     */
    public function makePayload(array $validData)
    {
        $payload = array_merge(
            $validData,
            ['api-key' => $this->api_key]
        );

        // isbn list accepted only as semi-colon delimited string
        if (isset($payload['isbn'])) {
            $payload['isbn'] = implode(self::LIST_GLUE, $payload['isbn']);
        }

        return $payload;
    }

    /**
     * Fetch data from remote URL
     *
     * @param array $payload
     * @return Illuminate\Http\Client\Response
     */
    public function fetchRemoteJson(array $payload = [])
    {
        try {
            $response = Http::timeout(self::TIMEOUT)->get($this->api_url, $payload);
        } catch (\Throwable $e) {
            return null;
        }

        return $response->json();
    }

}
