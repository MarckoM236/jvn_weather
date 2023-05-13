<?php

namespace App\Http\Controllers;

use App\Models\info_weathers;
use Carbon\Carbon;
use Illuminate\Http\Request;

use GuzzleHttp\Client;

class weatherController extends Controller
{
    private $client;
    private $api_key;
    private $endpoint;


    public function __construct()
    {
        $this->client = new Client();
        $this->api_key = '8d8a8ac02889077858fdf603054c94bc';
        $this->endpoint = 'api.openweathermap.org/data/2.5/weather';
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $history = info_weathers::take(10)->get();
        return view('home', ['history' => $history]);
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
        $history = info_weathers::take(10)->get();
        $city = $request->city;
        if (!empty($city)) {
            $response = $this->client->get($this->endpoint . '?q=' . $city . ',&APPID=' . $this->api_key);

            $statusCode = $response->getStatusCode();
            $jsonData = $response->getBody()->getContents();
            $data = json_decode($jsonData);

            if ($statusCode == 200) {
                $info_weather = new info_weathers();
                $info_weather->name_city = $city;
                $info_weather->latitude = $data->coord->lat;
                $info_weather->longitude = $data->coord->lon;
                $info_weather->temp = $data->main->temp;
                $info_weather->feels_like = $data->main->feels_like;
                $info_weather->temp_min = $data->main->temp_min;
                $info_weather->temp_max = $data->main->temp_max;
                $info_weather->pressure = $data->main->pressure;
                $info_weather->humidity = $data->main->humidity;
                $info_weather->save();
                $history = info_weathers::take(10)->get();
                return view('home', ['data' => $data, 'status' => $statusCode, 'history' => $history]);
            }
        } else {
            return view('home', ['msg' => 'Debe seleccionar una ciudad', 'history' => $history]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $history = info_weathers::take(10)->get();
        if (isset($id) && !empty($id)) {

            $history_detail = info_weathers::where('id', $id)->get()->first();

            if (isset($history_detail) && !empty($history_detail)) {
                $history_detail = $this->convertHistoryData($history_detail);
                $history_detail->main->format_created = $this->formatDate($history_detail->main->created_at);
            }
            
            return view('home', ['id' => $id, 'history' => $history, 'history_detail' => $history_detail]);
        } else {
            return view('home', ['history' => $history]);
        }
    }

    public function convertHistoryData(object $data): object
    {
        $history_detail = [
            "name" => $data->name_city,
            "coord" => [
                "lon" => $data->longitude,
                "lat" => $data->latitude
            ],
            "main" => [
                "temp" => $data->temp,
                "feels_like" => $data->feels_like,
                "temp_min" => $data->temp_min,
                "temp_max" => $data->temp_max,
                "pressure" => $data->pressure,
                "humidity" => $data->humidity,
                "created_at" => $data->created_at,
            ],
            "longitude" => $data->longitude,
            "latitude" => $data->latitude,
        ];

        /* convert all array content in onject */
        $history_detail = json_decode(json_encode($history_detail));

        return $history_detail;
    }

    public function formatDate(string $date): string
    {
        return Carbon::parse($date)->translatedFormat('d F Y');
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
