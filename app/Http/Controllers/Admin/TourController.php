<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourRequest;
use App\Http\Requests\Admin\UpdateTourRequest;
use App\Models\PickupPoint;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Handles tour CRUD operations for admin panel.
 */
final class TourController extends Controller
{
    /**
     * Display a listing of tours.
     */
    public function index(Request $request): InertiaResponse
    {
        $tours = Tour::withCount(['departures' => function ($query) {
            $query->where('date', '>=', now());
        }])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/tours/index', [
            'tours' => $tours,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new tour.
     */
    public function create(): InertiaResponse
    {
        $pickupPoints = PickupPoint::active()->ordered()->get();

        return Inertia::render('admin/tours/create', compact('pickupPoints'));
    }

    /**
     * Store a newly created tour in storage.
     */
    public function store(StoreTourRequest $request): RedirectResponse
    {
        $tour = Tour::create($request->validated());

        return redirect()
            ->route('admin.tours.index')
            ->with('success', "Tour '{$tour->name}' created successfully.");
    }

    /**
     * Display the specified tour.
     */
    public function show(Tour $tour): InertiaResponse
    {
        $tour->load(['departures' => function ($query) {
            $query->where('date', '>=', now())
                ->orderBy('date')
                ->limit(10);
        }]);

        return Inertia::render('admin/tours/show', compact('tour'));
    }

    /**
     * Show the form for editing the specified tour.
     */
    public function edit(Tour $tour): InertiaResponse
    {
        $pickupPoints = PickupPoint::active()->ordered()->get();

        return Inertia::render('admin/tours/create', compact('tour', 'pickupPoints'));
    }

    /**
     * Update the specified tour in storage.
     */
    public function update(UpdateTourRequest $request, Tour $tour): RedirectResponse
    {
        $tour->update($request->validated());

        return redirect()
            ->route('admin.tours.index')
            ->with('success', "Tour '{$tour->name}' updated successfully.");
    }

    /**
     * Remove the specified tour from storage.
     */
    public function destroy(Tour $tour): RedirectResponse
    {
        $tourName = $tour->name;

        // Check for future bookings before deleting (exclude all final-state bookings)
        $futureBookings = $tour->departures()
            ->where('date', '>=', now())
            ->whereHas('bookings', function ($query) {
                $query->whereNotIn('status', ['cancelled', 'completed', 'rejected', 'expired']);
            })
            ->count();

        if ($futureBookings > 0) {
            return redirect()
                ->route('admin.tours.index')
                ->with('error', "Cannot delete tour '{$tourName}' - it has active future bookings.");
        }

        $tour->delete();

        return redirect()
            ->route('admin.tours.index')
            ->with('success', "Tour '{$tourName}' deleted successfully.");
    }
}
