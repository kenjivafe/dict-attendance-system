<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DtrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::parse('2025-02-01', 'Asia/Manila'); // Start date
        $endDate = Carbon::yesterday('Asia/Manila'); // End date is yesterday
        $userIds = [2, 3, 4, 5, 6, 7]; // Excluding user_id 1 (admin)

        while ($startDate->lte($endDate)) {
            if ($startDate->isWeekday()) { // Only Monday to Friday
                foreach ($userIds as $userId) {
                    Attendance::create([
                        'date' => $startDate->toDateString(),
                        'user_id' => $userId,
                        'time_in_am' => $this->randomTime('08:00', '08:15'),
                        'time_out_am' => $this->randomTime('12:00', '12:05'),
                        'time_in_pm' => $this->randomTime('13:00', '13:15'),
                        'time_out_pm' => $this->randomTime('17:00', '17:05'),
                    ]);
                }
            }
            $startDate->addDay();
        }
    }

    private function randomTime($start, $end)
    {
        // Ensure correct timezone
        $startTime = Carbon::createFromFormat('H:i', $start, 'Asia/Manila');
        $endTime = Carbon::createFromFormat('H:i', $end, 'Asia/Manila');

        // Get the difference in minutes
        $minutesDiff = $startTime->diffInMinutes($endTime);

        // Add a random number of minutes within the valid range
        $randomMinutes = rand(0, $minutesDiff);
        $randomTime = $startTime->copy()->addMinutes($randomMinutes);

        return $randomTime->format('H:i:s'); // Ensure correct format
    }
}
