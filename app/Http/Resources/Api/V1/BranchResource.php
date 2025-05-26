<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use App\Http\Resources\Api\SimpleCityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
39     * Transform the resource into an array.
40     *
41     * @return array<string, mixed>
42     */
    public function toArray(Request $request): array
    {
        $dayNames = [
            0 => __('mobile.sunday'),
            1 => __('mobile.monday'),
            2 => __('mobile.tuesday'),
            3 => __('mobile.wednesday'),
            4 => __('mobile.thursday'),
            5 => __('mobile.friday'),
            6 => __('mobile.saturday'),
        ];

        // Parse working days and hours safely
        $workingDays = is_array($this->working_days) 
            ? $this->working_days 
            : (json_decode($this->working_days, true) ?? []);
            
        $workingHours = is_array($this->working_hours)
            ? $this->working_hours
            : (json_decode($this->working_hours, true) ?? []);

        // Build working schedule for all days (0 to 6)
        $workingSchedule = [];
        for ($day = 0; $day <= 6; $day++) {
            $index = array_search($day, $workingDays);
            $workingSchedule[] = [
                'day' => $dayNames[$day],
                'hours' => $index !== false && isset($workingHours[$index]) && is_array($workingHours[$index])
                    ? [
                        'open' => $workingHours[$index]['open'] ?? null,
                        'close' => $workingHours[$index]['close'] ?? null,
                    ]
                    :null,
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => SimpleCityResource::make($this->city),
            'working_schedule' => $workingSchedule,
            'image' => asset('banner.png'),
        ];
    }
}