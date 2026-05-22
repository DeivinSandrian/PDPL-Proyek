<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'vehicle_type' => 'nullable|string|max:50',
            'capacity' => 'required|integer',
            'status' => 'nullable|string|max:20',
        ]);

        $vehicle = Vehicle::create($validated);
        return response()->json($vehicle, 201);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle->load('seats'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'string|max:20',
            'vehicle_type' => 'string|max:50',
            'capacity' => 'integer',
            'status' => 'string|max:20',
        ]);

        $vehicle->update($validated);
        return response()->json($vehicle);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(null, 204);
    }
}
