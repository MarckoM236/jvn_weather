<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

class weatherController extends Controller
{
    private $client;
    private $api_key;
    private $endpoint;

    public function __construct(){
        $this->client = new Client();
        $this->api_key= '8d8a8ac02889077858fdf603054c94bc';
        $this->endpoint= 'api.openweathermap.org/data/2.5/weather';
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $city=$request->city;
        $response = $this->client->get($this->endpoint.'?q='.$city.',&APPID='.$this->api_key);

        $statusCode = $response->getStatusCode();
        $jsonData = $response->getBody()->getContents();
        $data = json_decode($jsonData);
        return view('home',['data'=>$data,'status'=>$statusCode]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
