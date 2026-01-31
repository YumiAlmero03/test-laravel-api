<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocaleRequest extends FormRequest
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
        // Check if route has a locale (update), otherwise null (create)
        $localeId = $this->route('locale')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('locales', 'code')->ignore($localeId),
            ],
            'name' => 'required|string|max:50',
        ];
    }
}
