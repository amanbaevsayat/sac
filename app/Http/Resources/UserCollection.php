<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class UserCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => UserResource::collection($this->collection),
            'others' => [],
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
                    'title' => 'Аккаунт',
                    'key' => 'account',
                ],
                [
                    'title' => 'E-mail',
                    'key' => 'email',
                ],
                [
                    'title' => 'Роль',
                    'key' => 'role_id',
                ],
                [
                    'title' => 'Телефон',
                    'key' => 'phone',
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
