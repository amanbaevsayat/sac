<?php

namespace App\Http\Resources;

use App\Models\Remark;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class ProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => ProductResource::collection($this->collection),
            'others' => [],
            'dataTitles' => [
                [
                    'title' => 'ID',
                    'key' => 'id',
                ],
                [
                    'title' => 'Код',
                    'key' => 'code',
                ],
                [
                    'title' => 'Заголовок',
                    'key' => 'title',
                ],
                [
                    'title' => 'Описание',
                    'key' => 'description',
                ],
                [
                    'title' => 'Цена',
                    'key' => 'price',
                ],
                [
                    'title' => 'Пробная цена',
                    'key' => 'trial_price',
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
