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
            $customersQuery = Customer::query();
            $phone = preg_replace('/[^0-9]/', '', $data['text']);
            $query = "(name like '%{$data['text']}%'";
            if (!empty($phone)){
                $query .= " OR phone like '%{$phone}%' OR '{$phone}' LIKE CONCAT('%', phone, '%')";
            }
            $query .= ")";
            $result = $customersQuery->whereRaw($query)->get()->map(function($item, $index) {
                return [
                    "value" => $item["id"],
                    "label" => $item["name_with_phone"],
                ];
            });
            // $result = Customer::where('name', 'LIKE', "%{$data['text']}%")->orWhere('phone', 'LIKE', "%{$data['text']}%")->get()->map(function($item, $index) {
            //     return [
            //         "value" => $item["id"],
            //         "label" => $item["name_with_phone"],
            //     ];
            // });
        }

        return response()->json([
            'data' => $result,
        ]);
    }
}
