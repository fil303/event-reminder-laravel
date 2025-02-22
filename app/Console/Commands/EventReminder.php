<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReminder as EventReminderMail;

class EventReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for upcoming event and send reminder email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::whereStatus(0)->get();
        foreach($events as $event){
            $now = Carbon::now();
            $date= Carbon::parse($event->date);

            if($date->lessThanOrEqualTo($now)){
                $emails = explode(",", $event->emails);
                $emails = array_filter($emails);
                foreach($emails as $email){
                    Mail::to($email)->queue(new EventReminderMail($event));
                }
                $event->status = 1;
                $event->save();
            }
        }
    }
}
