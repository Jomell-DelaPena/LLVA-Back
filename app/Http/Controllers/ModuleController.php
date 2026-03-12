<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /** GET /modules */
    public function index()
    {
        return response()->json(
            Module::withCount('accesses')->orderBy('name')->get()
        );
    }

    /** POST /modules */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:100|unique:modules,code',
            'description' => 'nullable|string',
            'active'      => 'boolean',
        ]);

        $module = Module::create($data);

        return response()->json($module->loadCount('accesses'), 201);
    }

    /** PUT /modules/{module} */
    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:100|unique:modules,code,' . $module->id,
            'description' => 'nullable|string',
            'active'      => 'boolean',
        ]);

        $module->update($data);

        return response()->json($module->loadCount('accesses'));
    }

    /** DELETE /modules/{module} */
    public function destroy(Module $module)
    {
        $module->delete();

        return response()->json(null, 204);
    }
}
