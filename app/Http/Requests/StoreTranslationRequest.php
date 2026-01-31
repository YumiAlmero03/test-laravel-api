<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTranslationRequest extends FormRequest
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
        $translationId = $this->route('translation')?->id;

        return [
            'locale_id' => ['required', 'exists:locales,id'],

            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('translations', 'key')
                    ->where(fn ($q) => $q->where('locale_id', $this->input('locale_id'))
                    )
                    ->ignore($translationId),
            ],

            'value' => [
                'required',
                'string',
                Rule::unique('translations', 'value')
                    ->where(fn ($q) => $q->where('locale_id', $this->input('locale_id'))
                    )
                    ->ignore($translationId),
            ],

            'tags' => ['array'],
        ];
    }
}
