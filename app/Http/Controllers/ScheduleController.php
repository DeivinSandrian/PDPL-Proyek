<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['vehicle', 'route']);

        if ($request->has('origin')) {
            $query->whereHas('route', function($q) use ($request) {
                $q->where('origin_city', 'like', '%' . $request->origin . '%');
            });
        }

        if ($request->has('destination')) {
            $query->whereHas('route', function($q) use ($request) {
                $q->where('destination_city', 'like', '%' . $request->destination . '%');
            });
        }

        $schedules = $query->get();
        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'route_id' => 'required|exists:routes,route_id',
            'departure_time' => 'required|date',
            'arrival_estimate' => 'required|date|after:departure_time',
            'price' => 'required|numeric',
            'status' => 'nullable|string|max:20',
        ]);

        $schedule = Schedule::create($validated);
        return response()->json($schedule, 201);
    }

    public function show(Schedule $schedule)
    {
        return response()->json($schedule->load(['vehicle', 'route']));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'vehicle_id' => 'exists:vehicles,vehicle_id',
            'route_id' => 'exists:routes,route_id',
            'departure_time' => 'date',
            'arrival_estimate' => 'date|after:departure_time',
            'price' => 'numeric',
            'status' => 'string|max:20',
        ]);

        $schedule->update($validated);
        return response()->json($schedule);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->json(null, 204);
    }
}
