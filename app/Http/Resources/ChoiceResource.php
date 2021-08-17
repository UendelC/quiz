<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array_data = [
            'description' => $this->description,
            'id' => $this->id
        ];

        if (auth()->user()->type == 'teacher') {
            $array_data['is_right'] = $this->is_right;
        }

        return $array_data;
    }
}
