<?php

namespace App\Http\Controllers;

use App\Models\IncidentType;
use Illuminate\Http\Request;

class IncidentTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = IncidentType::orderBy('name');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50|unique:incident_types,code',
            'description' => 'nullable|string',
            'active'      => 'boolean',
        ]);

        $incidentType = IncidentType::create($data);

        return response()->json($incidentType, 201);
    }

    public function update(Request $request, IncidentType $incidentType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50|unique:incident_types,code,' . $incidentType->id,
            'description' => 'nullable|string',
            'active'      => 'boolean',
        ]);

        $incidentType->update($data);

        return response()->json($incidentType);
    }

    public function destroy(IncidentType $incidentType)
    {
        $incidentType->delete();

        return response()->json(null, 204);
    }
}
