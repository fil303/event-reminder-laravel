<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class EventSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $emails = explode(",", $this->emails);
        $emails = array_filter($emails);
        foreach($emails as $email){
            preg_match("%^[a-zA-Z0-9].+@[a-zA-Z0-9].+.com$%", $email, $match);
            if(!$match) response()->json(['status' => false, 'message' => "Email invalid", "data" => null])->throwResponse();
        }
        
        return [
            "id"   => "required",
            "name" => "required",
            "date" => "required|after_or_equal:" . date("Y-m-d"),
            "description" => "required",
            "emails" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            "date.after_or_equal" => "Select a valid date, today or future day"
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $error = $validator->errors()->all()[0] ?? "Something went wrong with validation";
        response()->json(['status' => false, 'message' => $error, "data" => null])->throwResponse();
    }
}
