<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id, // Include patient ID
            'doctor_id' => $this->doctor_id,   // Include doctor ID
            'appointment_time' => $this->appointment_time,
            'status' => $this->status,
            'created_at' => $this->created_at, // Optional: include created_at if needed
            'updated_at' => $this->updated_at, // Optional: include updated_at if needed
        ];
    }
}
