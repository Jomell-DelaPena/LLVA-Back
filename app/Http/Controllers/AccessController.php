<?php

namespace App\Http\Controllers;

use App\Models\Access;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    /** GET /accesses?module_id= */
    public function index(Request $request)
    {
        $query = Access::with('module');

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }

        return response()->json($query->orderBy('name')->get());
    }

    /** POST /accesses */
    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:100|unique:accesses,code',
            'description' => 'nullable|string',
            'active'      => 'boolean',
        ]);

        $access = Access::create($data);

        return response()->json($access->load('module'), 201);
    }

    /** PUT /accesses/{access} */
    public function update(Request $request, Access $access)
    {
        $data = $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:100|unique:accesses,code,' . $access->id,
            'description' => 'nullable|string',
            'active'      => 'boolean',
        ]);

        $access->update($data);

        return response()->json($access->load('module'));
    }

    /** DELETE /accesses/{access} */
    public function destroy(Access $access)
    {
        // Block deletion if this access is assigned to any users
        if ($access->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete: this access is assigned to one or more users.',
            ], 422);
        }

        $access->delete();

        return response()->json(null, 204);
    }
}
