<?php

namespace App\Http\Resources;

use App\Models\Remark;
use App\Models\Subscription;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class UserLogCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => UserLogResource::collection($this->collection),
            'others' => [
            ],
            'dataTitles' => [
                [
                    'title' => 'ID абонемента',
                ],
                [
                    'title' => 'Клиенты',
                ],
                [
                    'title' => 'Телефон',
                ],
                [
                    'title' => 'Тип лога',
                ],
                [
                    'title' => 'Описание',
                ],
                [
                    'title' => 'User',
                ],
                [
                    'title' => 'Время',
                    'key' => 'created_at',
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
