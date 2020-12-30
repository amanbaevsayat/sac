<?php

namespace App\Http\Resources;

use App\Models\Remark;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class CustomerCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => CustomerResource::collection($this->collection),
            'others' => [
            ],
            'dataTitles' => [
                [
                    'title' => 'ID',
                    'key' => 'id',
                ],
                [
                    'title' => 'Имя',
                    'key' => 'name',
                ],
                [
                    'title' => 'Телефон',
                    'key' => 'phone',
                ],
                [
                    'title' => 'E-mail',
                    'key' => 'email',
                ],
                [
                    'title' => 'Абонементы',
                    // 'key' => 'email',
                ],
            ],
            'pagination' => [
                'current_page' => $this->currentPage(),
                'first_page_url' => $this->url(1),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'last_page_url' => $this->url($this->lastPage()),
                'next_page_url' => $this->nextPageUrl(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'prev_page_url' => $this->previousPageUrl(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }
}
