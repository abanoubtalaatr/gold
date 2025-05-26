<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use App\Http\Resources\Api\SimpleCityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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

        // Parse working days and hours
        $workingDays = json_decode($this->working_days, true) ?? [];
        $workingHours = json_decode($this->working_hours, true) ?? [];

        // Combine days and hours into a structured array
        $workingSchedule = [];
        foreach ($workingDays as $index => $day) {
            if (isset($dayNames[$day], $workingHours[$index])) {
                $workingSchedule[] = [
                    'day' => $dayNames[$day],
                    'hours' => [
                        'open' => $workingHours[$index]['open'],
                        'close' => $workingHours[$index]['close'],
                    ],
                ];
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => SimpleCityResource::make($this->city),
            'working_schedule' => $workingSchedule,
        ];
    }
}