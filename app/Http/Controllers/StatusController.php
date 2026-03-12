<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $query = Status::orderBy('name');

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
            'name'   => 'required|string|max:255',
            'code'   => 'nullable|string|max:50|unique:statuses,code',
            'color'  => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'active' => 'boolean',
        ]);

        $status = Status::create($data);

        return response()->json($status, 201);
    }

    public function update(Request $request, Status $status)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'code'   => 'nullable|string|max:50|unique:statuses,code,' . $status->id,
            'color'  => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'active' => 'boolean',
        ]);

        $status->update($data);

        return response()->json($status);
    }

    public function destroy(Status $status)
    {
        $status->delete();

        return response()->json(null, 204);
    }
}
