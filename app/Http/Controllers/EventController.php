<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\EventSaveRequest;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::orderBy("date")->get();
        return view("event", [ "events" => $events ]);
    }

    public function store(EventSaveRequest $request): mixed
    {
        $id = [ "id" => $request->id ];
        $status = date("T-m-d H:i:s") < date("T-m-d H:i:s", strtotime($request->date))
                ? 0 : 1;
        $newEvent = [
            "name" => $request->name,
            "date" => $request->date,
            "description" => $request->description,
            "emails" => $request->emails,
            "status" => $status
        ];

        try {
            $save = Event::updateOrCreate($id, $newEvent);
        } catch (\Exception $e) {
            $save = false;
        }

        if($save){
            return response()->json([
                "status"  => true,
                "message" => "Event Saved Successfully",
                "data"    => Event::orderBy("date")->get()
            ]);
        }

        return response()->json([
            "status"  => false,
            "message" => "Event Failed To Save!!",
            "data"    => null
        ]);
    }

    public function delete(string $id): mixed
    {
        if(!$event = Event::where("id", $id))
            return response()->json([
                "status"  => false,
                "message" => "Event Not Found!!",
                "data"    => null
            ]);

        if($event->delete()){
            return response()->json([
                "status"  => true,
                "message" => "Event Deleted Successfully",
                "data"    => Event::orderBy("date")->get()
            ]);
        }

        return response()->json([
            "status"  => false,
            "message" => "Event Failed To Delete!!",
            "data"    => null
        ]);
    }
}
