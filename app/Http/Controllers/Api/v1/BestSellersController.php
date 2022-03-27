<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Services\NYTAPIService;
use App\Http\Controllers\Controller;

class BestSellersController extends Controller
{
    protected $apiService;

    public function __construct(NYTAPIService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Handle bestsellers API GET
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function apiGet(Request $request)
    {
        $fetchedData = $this->apiService->fetch($request);

        return response()->json($fetchedData);
    }
}
