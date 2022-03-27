<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Services\NYTAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NYTAPIServiceTest extends TestCase
{
    /**
     * Test for all data pass
     *
     * @return void
     */
    public function testValidateAllPass()
    {
        $passArray = [
            'author' => 'Sophia',
            'isbn' => [
                '9780446579933',
                '039916927X',
            ],
            'title' => "'TIS THE SEASON",
            'offset' => 20,
        ];
        $request = Request::create('/store', 'GET', $passArray);

        $apiService = new NYTAPIService();
        $validated = $apiService->validate($request);

        $this->assertEquals($passArray, $validated);
    }

    /**
     * Test for no data pass
     *
     * @return void
     */
    public function testValidateNonePass()
    {
        $passArray = [];
        $request = Request::create('/store', 'GET', $passArray);

        $apiService = new NYTAPIService();
        $validated = $apiService->validate($request);

        $this->assertEquals($passArray, $validated);
    }

    /**
     * Test for isbn invalid fail
     *
     * @return void
     */
    public function testValidateIsbnBadFail()
    {
        $request = Request::create('/store', 'GET', [
            'isbn' => ['12345'],
        ]);

        $apiService = new NYTAPIService();
        
        $this->expectException( ValidationException::class );

        $validated = $apiService->validate($request);
    }

    /**
     * Test for isbn malformed fail
     *
     * @return void
     */
    public function testValidateIsbnMalformedFail()
    {
        $request = Request::create('/store', 'GET', [
            'isbn' => '9780446579933'
        ]);

        $apiService = new NYTAPIService();
        
        $this->expectException( ValidationException::class );

        $validated = $apiService->validate($request);
    }

    /**
     * Test for bad offset fail
     *
     * @return void
     */
    public function testValidateOffsetBadFail()
    {
        $request = Request::create('/store', 'GET', [
            'offset' => 23,
        ]);

        $apiService = new NYTAPIService();

        $this->expectException( ValidationException::class );

        $validated = $apiService->validate($request);
    }

    /**
     * Test for payload pass
     *
     * @return void
     */
    public function testMakePayloadPass()
    {
        $validatedData = [
            'author' => 'Sophia',
            'isbn' => [
                '9780446579933',
                '039916927X',
            ],
            'title' => "'TIS THE SEASON",
            'offset' => 20,
        ];

        \Config::set('remoteapi.nyt_api_key', 'example-key');
        \Config::set('remoteapi.nyt_api_key', 'example-key');

        $apiService = new NYTAPIService();
        
        $payload = $apiService->makePayload($validatedData);

        $this->assertEquals([
            'author' => "Sophia",
            'isbn' => "9780446579933;039916927X",
            'title' => "'TIS THE SEASON",
            'offset' => 20,
            'api-key' => 'example-key',
        ], $payload);
    }

    /**
     * Test for null payload pass
     *
     * @return void
     */
    public function testMakePayloadNullPass()
    {
        $validatedData = [];

        \Config::set('remoteapi.nyt_api_key', 'example-key');
        \Config::set('remoteapi.nyt_api_url_bestsellers', 'http://www.notarealwebsite.com/');

        $apiService = new NYTAPIService();
        
        $payload = $apiService->makePayload($validatedData);

        $this->assertEquals([
            'api-key' => 'example-key',
        ], $payload);
    }

    /**
     * Test for fetchRemoteData pass
     *
     * @return void
     */
    public function testFetchRemoteJsonRequestOk()
    {
        \Config::set('remoteapi.nyt_api_key', 'example-key');
        \Config::set('remoteapi.nyt_api_url_bestsellers', 'totally.a.real.url');

        Http::fake([
            '*' => Http::response('{"status":"OK"}', 200, ['Headers'])
        ]);

        $apiService = new NYTAPIService();
        
        $data = $apiService->fetchRemoteJson([
            'author' => "Sophia",
            'isbn' => "9780446579933;039916927X",
            'title' => "'TIS THE SEASON",
            'offset' => 20,
            'api-key' => 'example-key',
        ]);

        $this->assertIsArray($data);
    }


    /**
     * Test for fetchRemoteData pass
     *
     * @return void
     */
    public function testFetchRemoteJsonRequestFail()
    {
        \Config::set('remoteapi.nyt_api_key', 'example-key');
        \Config::set('remoteapi.nyt_api_url_bestsellers', 'totally.a.real.url');

        Http::fake([
            '*' => Http::response('', 503, ['Headers'])
        ]);

        $apiService = new NYTAPIService();
        
        $data = $apiService->fetchRemoteJson([
            'author' => "Sophia",
            'isbn' => "9780446579933;039916927X",
            'title' => "'TIS THE SEASON",
            'offset' => 20,
            'api-key' => 'example-key',
        ]);

        $this->assertNull($data);
    }

}
