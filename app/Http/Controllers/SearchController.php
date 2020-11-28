<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $data = $request->all();
        $result = [];
        if ($data['key'] == 'customer') {
            $result = Customer::where('name', 'LIKE', "%{$data['text']}%")->orWhere('phone', 'LIKE', "%{$data['text']}%")->get()->map(function($item, $index) {
                return [
                    "value" => $item["id"],
                    "label" => $item["name_with_phone"],
                ];
            });
        }

        return response()->json([
            'data' => $result,
        ]);
    }
}
