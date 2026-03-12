<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    /**
     * GET /users/{user}/accesses
     * Returns the user's all_access flag + current access IDs,
     * plus all modules with their accesses (and a `granted` bool per access).
     */
    public function index(User $user)
    {
        $grantedIds = $user->accesses()->pluck('accesses.id')->toArray();

        $modules = Module::where('active', true)
            ->with(['accesses' => fn ($q) => $q->where('active', true)->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(function ($module) use ($grantedIds) {
                return [
                    'id'       => $module->id,
                    'name'     => $module->name,
                    'code'     => $module->code,
                    'accesses' => $module->accesses->map(fn ($a) => [
                        'id'      => $a->id,
                        'name'    => $a->name,
                        'code'    => $a->code,
                        'granted' => in_array($a->id, $grantedIds),
                    ])->values(),
                ];
            });

        return response()->json([
            'all_access' => (bool) $user->all_access,
            'access_ids' => $grantedIds,
            'modules'    => $modules,
        ]);
    }

    /**
     * PUT /users/{user}/accesses
     * Updates the user's all_access flag and syncs the user_accesses pivot.
     */
    public function sync(Request $request, User $user)
    {
        $data = $request->validate([
            'all_access' => 'required|boolean',
            'access_ids' => 'array',
            'access_ids.*' => 'exists:accesses,id',
        ]);

        $user->update(['all_access' => $data['all_access']]);

        // Build pivot data: each access_id gets granted_by set to the current user
        $syncData = [];
        foreach ($data['access_ids'] ?? [] as $accessId) {
            $syncData[$accessId] = ['granted_by' => $request->user()->id];
        }

        $user->accesses()->sync($syncData);

        return response()->json([
            'all_access' => (bool) $user->all_access,
            'access_ids' => array_keys($syncData),
        ]);
    }
}
