<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailableSeatsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'trip_id' => [
                'required',
                'exists:trips,hash_id'
            ],
            'from_id' => [
                'required',
                'exists:trip_stations,hash_id'
            ],
            'to_id'   => [
                'required',
                'exists:trip_stations,hash_id'
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
