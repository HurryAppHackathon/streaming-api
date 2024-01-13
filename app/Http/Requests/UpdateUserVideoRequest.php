<?php

namespace App\Http\Requests;

use App\Models\UserVideo;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $video = $this->route('user_video');

        return $this->user()->id === $video->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ];
    }
}
