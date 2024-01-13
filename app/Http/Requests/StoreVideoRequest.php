<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'thumbnail' => 'required|file|mimetypes:image/png,image/jpeg,image/webp|extensions:jpg,jpeg,jpe,png,webp',
            'file' => 'required|file|mimetypes:video/mpeg,video/mp4,video/webm|extensions:mp4,webm',
            'is_public' => 'required|string|boolean',
        ];
    }
}
