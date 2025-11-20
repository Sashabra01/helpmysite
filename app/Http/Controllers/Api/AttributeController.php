<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;

class AttributeController extends Controller
{
    public function index(): JsonResponse
    {
        $attributes = Attribute::with(['values' => function($query) {
            $query->orderBy('value');
        }])
        ->where('is_filterable', true)
        ->get();

        return response()->json($attributes);
    }
}
