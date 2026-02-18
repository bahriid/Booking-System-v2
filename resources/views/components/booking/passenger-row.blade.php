<div class="d-flex gap-3 align-items-start mb-3 passenger-row" data-index="{{ $index }}">
    <div class="flex-shrink-0 pt-3">
        <x-ui.badge :variant="$typeColor()" light>
            {{ $type->shortCode() }}
        </x-ui.badge>
    </div>
    <div class="flex-grow-1">
        <div class="row g-3">
            <div class="col-md-6">
                <input
                    type="text"
                    name="passengers[{{ $index }}][name]"
                    value="{{ old("passengers.{$index}.name", $name) }}"
                    class="form-control form-control-sm"
                    placeholder="Passenger name"
                >
            </div>
            <div class="col-md-5">
                <input
                    type="text"
                    name="passengers[{{ $index }}][pickup]"
                    value="{{ old("passengers.{$index}.pickup", $pickup) }}"
                    class="form-control form-control-sm"
                    placeholder="Pickup location"
                >
            </div>
            <input type="hidden" name="passengers[{{ $index }}][type]" value="{{ $type->value }}">
        </div>
    </div>
    @if($removable)
        <div class="flex-shrink-0 pt-2">
            <button type="button" class="btn btn-icon btn-sm btn-light-danger remove-passenger" title="Remove passenger">
                <x-ui.icon name="trash" class="fs-6" />
            </button>
        </div>
    @endif
</div>
