<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userVideo = $this->route('user_video');
        $socketsToken = $this->query('_sockets_token');

        return $socketsToken === env('SOCKETS_TOKEN') || $userVideo->is_public || $this->user()->id === $userVideo->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
