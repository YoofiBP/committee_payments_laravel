<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //TODO: Flyer should be image so modify validation for that
        return [
            'name' => ['required'],
            'venue' => ['required'],
            'event_date' => ['required','date_format:'.config('constants.date_format'),'after:today'],
            'flyer' => ['required']
        ];
    }
}
