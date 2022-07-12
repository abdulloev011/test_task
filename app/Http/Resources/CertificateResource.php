<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            'name' => $this->user->name,
            'last_name' => $this->user->last_name,
            'number_of_tree' => $this->number_of_tree,
            'price' => $this->price,
            'activate_date' =>Carbon::parse($this->updated_at)->format('Y-m-d H:i')
        ];
    }
}
