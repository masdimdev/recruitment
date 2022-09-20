<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $attributes = $this->trans_attributes;
        foreach ($attributes as $key => $value) {
            if (substr($value, 0, 2) == '__') {
                $attributes[$key] = __(substr($value, 2));
            }
        }

        return [
            'id' => $this->id,
            'header' => __($this->header_key, $attributes),
            'content' => __($this->content_key, $attributes),
        ];
    }
}
