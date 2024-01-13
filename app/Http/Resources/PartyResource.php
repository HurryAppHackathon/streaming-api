<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'image_url' => $this->image_url,
            'is_public' => $this->is_public,
            'invite_code' => $this->when($request->user()->id === $this->user_id, $this->invite_code),
            'owner' => $this->whenLoaded('owner'),
            'finished_at' => $this->finished_at,
            'created_at' => $this->created_at,
        ];
    }
}
