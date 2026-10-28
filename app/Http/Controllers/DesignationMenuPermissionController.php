<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignationMenuPermissionController extends Controller
{
    public function edit(Designation $designation)
    {
        $this->ensureSuperAdmin();

        $menus = Menu::active()
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');

        $grantedMenuIds = $designation->menuPermissions()
            ->where('can_view', true)
            ->pluck('menu_id')
            ->all();

        return view('designations.menu-permissions', compact('designation', 'menus', 'grantedMenuIds'));
    }

    public function update(Request $request, Designation $designation)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:menus,id'],
        ]);

        $activeMenuIds = Menu::active()->pluck('id')->all();
        $selectedMenuIds = array_map('intval', $validated['menu_ids'] ?? []);

        DB::transaction(function () use ($designation, $activeMenuIds, $selectedMenuIds) {
            foreach ($activeMenuIds as $menuId) {
                $designation->menuPermissions()->updateOrCreate(
                    ['menu_id' => $menuId],
                    ['can_view' => in_array($menuId, $selectedMenuIds, true)]
                );
            }
        });

        return redirect()
            ->route('designations.menu-permissions.edit', $designation)
            ->with('success', "Menu permissions for {$designation->name} updated successfully.");
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }
}
