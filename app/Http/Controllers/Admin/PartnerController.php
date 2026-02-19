<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Partner\CreatePartnerAction;
use App\Actions\Partner\UpdatePartnerPriceListAction;
use App\Enums\PartnerType;
use App\Enums\PaxType;
use App\Enums\Season;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePartnerRequest;
use App\Http\Requests\Admin\UpdatePartnerRequest;
use App\Models\Partner;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Handles partner CRUD operations for admin panel.
 */
final class PartnerController extends Controller
{
    /**
     * Display a listing of partners.
     */
    public function index(Request $request): InertiaResponse
    {
        $partners = Partner::withCount([
            'bookings' => function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            },
        ])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(request('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/partners/index', [
            'partners' => $partners,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create(): InertiaResponse
    {
        $tours = Tour::active()->orderBy('name')->get();
        $partnerTypes = collect(PartnerType::cases())->map(fn ($t) => ['value' => $t->value, 'label' => $t->label()])->values()->all();
        $seasons = collect(Season::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()])->values()->all();
        $paxTypes = collect(PaxType::cases())->map(fn ($t) => ['value' => $t->value, 'label' => $t->label()])->values()->all();

        return Inertia::render('admin/partners/create', compact('tours', 'partnerTypes', 'seasons', 'paxTypes'));
    }

    /**
     * Store a newly created partner in storage.
     */
    public function store(StorePartnerRequest $request, CreatePartnerAction $action): RedirectResponse
    {
        $partner = $action->execute($request->validated());

        return redirect()
            ->route('admin.partners.show', $partner)
            ->with('success', "Partner '{$partner->name}' created successfully.");
    }

    /**
     * Display the specified partner.
     */
    public function show(Partner $partner): InertiaResponse
    {
        $partner->load([
            'bookings' => function ($query) {
                $query->with(['tourDeparture.tour'])
                    ->latest()
                    ->limit(10);
            },
            'payments' => function ($query) {
                $query->latest()->limit(10);
            },
            'priceLists.tour',
        ]);

        // Get all tours for price list display
        $tours = Tour::active()->orderBy('name')->get();

        // Build price matrix for display
        $priceMatrix = [];
        foreach ($partner->priceLists as $priceList) {
            $priceMatrix[$priceList->tour_id][$priceList->season->value][$priceList->pax_type->value] = $priceList->price;
        }

        return Inertia::render('admin/partners/show', compact('partner', 'tours', 'priceMatrix'));
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner): InertiaResponse
    {
        $tours = Tour::active()->orderBy('name')->get();
        $partnerTypes = collect(PartnerType::cases())->map(fn ($t) => ['value' => $t->value, 'label' => $t->label()])->values()->all();
        $seasons = collect(Season::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()])->values()->all();
        $paxTypes = collect(PaxType::cases())->map(fn ($t) => ['value' => $t->value, 'label' => $t->label()])->values()->all();

        // Build price matrix for form
        $priceMatrix = [];
        foreach ($partner->priceLists as $priceList) {
            $priceMatrix[$priceList->tour_id][$priceList->season->value][$priceList->pax_type->value] = $priceList->price;
        }

        return Inertia::render('admin/partners/create', compact('partner', 'tours', 'partnerTypes', 'seasons', 'paxTypes', 'priceMatrix'));
    }

    /**
     * Update the specified partner in storage.
     */
    public function update(UpdatePartnerRequest $request, Partner $partner, UpdatePartnerPriceListAction $priceAction): RedirectResponse
    {
        $partner->update($request->validated());

        // Update associated user email if partner email changed
        if ($partner->wasChanged('email')) {
            $partner->users()->update(['email' => $partner->email]);
        }

        // Update user active status if partner status changed
        if ($partner->wasChanged('is_active')) {
            $partner->users()->update(['is_active' => $partner->is_active]);
        }

        // Update prices if provided
        if ($request->has('prices')) {
            $priceAction->execute($partner, $request->input('prices', []));
        }

        return redirect()
            ->route('admin.partners.show', $partner)
            ->with('success', "Partner '{$partner->name}' updated successfully.");
    }

    /**
     * Update the partner's price list.
     */
    public function updatePrices(Request $request, Partner $partner, UpdatePartnerPriceListAction $action): RedirectResponse
    {
        $prices = $request->input('prices', []);
        $count = $action->execute($partner, $prices);

        return redirect()
            ->route('admin.partners.show', $partner)
            ->with('success', "Updated {$count} price entries for '{$partner->name}'.");
    }

    /**
     * Remove the specified partner from storage.
     */
    public function destroy(Partner $partner): RedirectResponse
    {
        $partnerName = $partner->name;

        // Check for active bookings before deleting
        $activeBookings = $partner->bookings()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereHas('tourDeparture', function ($query) {
                $query->where('date', '>=', now());
            })
            ->count();

        if ($activeBookings > 0) {
            return redirect()
                ->route('admin.partners.index')
                ->with('error', "Cannot delete partner '{$partnerName}' - they have {$activeBookings} active future bookings.");
        }

        // Deactivate associated users instead of deleting
        $partner->users()->update(['is_active' => false]);

        $partner->delete();

        return redirect()
            ->route('admin.partners.index')
            ->with('success', "Partner '{$partnerName}' deleted successfully.");
    }
}
