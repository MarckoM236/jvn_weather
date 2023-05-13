<?php

namespace App\Http\Controllers;

use App\Models\info_weathers;
use Illuminate\Http\Request;

class HistoryWeather extends Controller
{
    public function delete(int $id)
    {
        $history = info_weathers::find($id);

        if(!$history){
            return redirect()->route('home')->message('History not found');
        }

        $history->delete();
        return redirect()->route('home');
    }

    public function clear()
    {
        $info=info_weathers::all();

        foreach ($info as $item) {
            $item->delete();
        }
        return redirect()->route('home');
    }
}
