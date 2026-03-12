<?php

namespace App\Http\Controllers;

use App\Models\NavItem;
use Illuminate\Http\Request;

class NavItemController extends Controller
{
    /**
     * GET /nav-items
     * Returns the full nav tree (top-level items with their children).
     * Active-only by default; pass ?all=true for management (includes inactive).
     */
    public function index(Request $request)
    {
        $query = NavItem::with(['children.module', 'module'])
            ->whereNull('parent_id')
            ->orderBy('sort_order');

        if (!$request->boolean('all')) {
            $query->where('active', true);
        }

        return response()->json($query->get());
    }

    /**
     * POST /nav-items
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'  => 'nullable|exists:nav_items,id',
            'module_id'  => 'nullable|exists:modules,id',
            'title'      => 'required|string|max:255',
            'icon'       => 'required|string|max:100',
            'route'      => 'nullable|string|max:255',
            'type'       => 'required|in:directory,item',
            'sort_order' => 'integer|min:0',
            'active'     => 'boolean',
        ]);

        $navItem = NavItem::create($data);

        return response()->json($navItem->load(['children.module', 'module']), 201);
    }

    /**
     * PUT /nav-items/{navItem}
     */
    public function update(Request $request, NavItem $navItem)
    {
        $data = $request->validate([
            'parent_id'  => 'nullable|exists:nav_items,id',
            'module_id'  => 'nullable|exists:modules,id',
            'title'      => 'required|string|max:255',
            'icon'       => 'required|string|max:100',
            'route'      => 'nullable|string|max:255',
            'type'       => 'required|in:directory,item',
            'sort_order' => 'integer|min:0',
            'active'     => 'boolean',
        ]);

        $navItem->update($data);

        return response()->json($navItem->load(['children.module', 'module']));
    }

    /**
     * DELETE /nav-items/{navItem}
     */
    public function destroy(NavItem $navItem)
    {
        if ($navItem->children()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete: this directory has child items. Remove them first.',
            ], 422);
        }

        $navItem->delete();

        return response()->json(null, 204);
    }
}
