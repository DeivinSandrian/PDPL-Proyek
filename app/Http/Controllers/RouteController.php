<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        return response()->json(Route::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_city' => 'required|string|max:100',
            'destination_city' => 'required|string|max:100',
            'distance_km' => 'nullable|integer',
            'duration' => 'nullable',
        ]);

        return response()->json(Route::create($validated), 201);
    }

    public function show(Route $route)
    {
        return response()->json($route->load('schedules'));
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'origin_city' => 'string|max:100',
            'destination_city' => 'string|max:100',
            'distance_km' => 'integer',
            'duration' => 'nullable',
        ]);

        $route->update($validated);
        return response()->json($route);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(null, 204);
    }
}
