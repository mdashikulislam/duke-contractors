<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class TestController extends Controller
{
    public function index()
    {
        return Carbon::now();
        $event = new Event;
        $event->name = 'A new event';
        $event->description = 'Event description'.'<br>'.'sdgdsg';
        $event->startDateTime = \Carbon\Carbon::now();
        $event->endDateTime = \Carbon\Carbon::now()->addHour();
        $event->save();
        return Event::get();
    }
}
