<?php

namespace App\Http\Controllers;

use App\Models\StopPoint;
use Illuminate\Http\Request;

class StopPointController extends Controller
{
    public function index()
    {
        return response()->json(StopPoint::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'type' => 'required|in:pickup,dropoff',
        ]);

        return response()->json(StopPoint::create($validated), 201);
    }

    public function show(StopPoint $stopPoint)
    {
        return response()->json($stopPoint);
    }

    public function update(Request $request, StopPoint $stopPoint)
    {
        $validated = $request->validate([
            'name' => 'string|max:100',
            'address' => 'string',
            'type' => 'in:pickup,dropoff',
        ]);

        $stopPoint->update($validated);
        return response()->json($stopPoint);
    }

    public function destroy(StopPoint $stopPoint)
    {
        $stopPoint->delete();
        return response()->json(null, 204);
    }
}
