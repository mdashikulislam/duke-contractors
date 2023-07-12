<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class TestController extends Controller
{
    public function index()
    {

        //return Carbon::now()->addHour().'-----'.Carbon::now()->format('Y-m-d H:i:s T');
//        return Carbon::now()->format('Y-m-d\TH:i:s');
        $event = new Event;
        $event->name = 'A new event';
        $event->description = 'Event description'.'<br>'.'sdgdsg';
        $event->startDateTime = Carbon::parse('2023-07-15 17:38:04');
        $event->endDateTime =  Carbon::parse('2023-07-15 18:38:04');
        $event->save();
        return Event::get();
    }
}
