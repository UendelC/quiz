<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'exam_id' => $this->id,
            $this->mergeWhen(
                $this->has('questions'),
                [
                    'questions' => QuestionResource::collection($this->questions),
                ]
            ),
        ];
    }
}
