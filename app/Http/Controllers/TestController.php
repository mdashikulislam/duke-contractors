<?php

namespace App\Http\Controllers;

use Spatie\GoogleCalendar\Event;

class TestController extends Controller
{
    public function index()
    {
        $event = new Event;
        $event->name = 'A new event';
        $event->description = 'Event description';
        $event->startDateTime = \Carbon\Carbon::now();
        $event->endDateTime = \Carbon\Carbon::now()->addHour();
        $event->save();
    }
}
