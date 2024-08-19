<?php

    namespace App\Console\Commands;

    use Carbon\Carbon;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Log;
    use Revolution\Google\Sheets\Facades\Sheets;
    use Illuminate\Support\Facades\DB;


    class UpdateEventsInfo extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'app:update-events-info';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = "This command updates the info for every single event's meetings";

        /**
         * Execute the console command.
         */
        public function handle(): void
        {
            $sheet = Sheets::spreadsheet(env('POST_SPREADSHEET_ID'))->sheet('Agenda')->get();
            $header = $sheet->pull(0);
            /*    dd($sheet,$header);*/
            $values = Sheets::collection($header, $sheet);
            $eventMeetings = array_values($values->toArray());
    //    dd($eventMeetings);
            foreach ($eventMeetings as $meeting){
                if($meeting['Name'] !== ""){
                    $startDate = \Illuminate\Support\Carbon::parse("{$meeting['Date']} {$meeting['StartTime']}");
                    $endDate = \Illuminate\Support\Carbon::parse("{$meeting['Date']} {$meeting['EndTime']}");
                    $formattedStartDate = $startDate->setTimezone('UTC')->toIso8601String();
                    $formattedEndDate = $endDate->setTimezone('UTC')->toIso8601String();
                    DB::table('event_meetings')->updateOrInsert(['internal_identifier' => $meeting['Identifier'],
                        'event_id' => 1],
                        [
                            'name' => $meeting['Name'],
                            'description' => $meeting['Description'],
                            'location' => $meeting['Location'],
                            'speaker' => $meeting['Speaker'],
                            'start_date' => $formattedStartDate,
                            'end_date' => $formattedEndDate,
                            'online_link' => $meeting['ZoomLink'],
                            'visible' => $meeting['Visible'],
                            'updated_at' => Carbon::now()->toDateTimeString()
                        ]);
                }
            }

            Log::info("The event meetings' info was updated successfully.");
        }
    }
