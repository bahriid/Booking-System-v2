<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePickupPointRequest;
use App\Http\Requests\Admin\UpdatePickupPointRequest;
use App\Models\PickupPoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Admin Pickup Point CRUD Controller.
 * Manages pickup points for passenger collection.
 */
final class PickupPointController extends Controller
{
    /**
     * Display a listing of pickup points.
     */
    public function index(): View
    {
        $pickupPoints = PickupPoint::ordered()->paginate(20);

        return view('admin.pickup-points.index', compact('pickupPoints'));
    }

    /**
     * Show the form for creating a new pickup point.
     */
    public function create(): View
    {
        return view('admin.pickup-points.create');
    }

    /**
     * Store a newly created pickup point in storage.
     */
    public function store(StorePickupPointRequest $request): RedirectResponse
    {
        // Get the next sort order
        $maxSortOrder = PickupPoint::max('sort_order') ?? 0;

        $pickupPoint = PickupPoint::create([
            'name' => $request->validated('name'),
            'location' => $request->validated('location'),
            'default_time' => $request->validated('default_time'),
            'sort_order' => $request->validated('sort_order') ?? ($maxSortOrder + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.pickup-points.index')
            ->with('success', "Pickup point '{$pickupPoint->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified pickup point.
     */
    public function edit(PickupPoint $pickupPoint): View
    {
        return view('admin.pickup-points.edit', compact('pickupPoint'));
    }

    /**
     * Update the specified pickup point in storage.
     */
    public function update(UpdatePickupPointRequest $request, PickupPoint $pickupPoint): RedirectResponse
    {
        $pickupPoint->update([
            'name' => $request->validated('name'),
            'location' => $request->validated('location'),
            'default_time' => $request->validated('default_time'),
            'sort_order' => $request->validated('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.pickup-points.index')
            ->with('success', "Pickup point '{$pickupPoint->name}' updated successfully.");
    }

    /**
     * Toggle pickup point active status.
     */
    public function toggleActive(PickupPoint $pickupPoint): RedirectResponse
    {
        $pickupPoint->update(['is_active' => !$pickupPoint->is_active]);

        $status = $pickupPoint->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.pickup-points.index')
            ->with('success', "Pickup point '{$pickupPoint->name}' has been {$status}.");
    }

    /**
     * Remove the specified pickup point from storage.
     */
    public function destroy(PickupPoint $pickupPoint): RedirectResponse
    {
        // Check if there are passengers using this pickup point
        $passengersCount = $pickupPoint->passengers()->count();

        if ($passengersCount > 0) {
            return redirect()
                ->route('admin.pickup-points.index')
                ->with('error', "Cannot delete '{$pickupPoint->name}': {$passengersCount} passengers are assigned to this pickup point. Deactivate it instead.");
        }

        $name = $pickupPoint->name;
        $pickupPoint->delete();

        return redirect()
            ->route('admin.pickup-points.index')
            ->with('success', "Pickup point '{$name}' deleted successfully.");
    }
}
