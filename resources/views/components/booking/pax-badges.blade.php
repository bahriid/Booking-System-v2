@if($hasPassengers())
    <div class="d-flex gap-1 align-items-center">
        @if($compact)
            <span class="badge badge-light-primary fs-8" title="Adults">{{ $adults }}A</span>
            @if($children > 0)
                <span class="badge badge-light-info fs-8" title="Children">{{ $children }}C</span>
            @endif
            @if($infants > 0)
                <span class="badge badge-light-warning fs-8" title="Infants">{{ $infants }}I</span>
            @endif
        @else
            <span class="badge badge-light-primary" title="Adults">
                <x-ui.icon name="user" class="fs-7 me-1" />{{ $adults }}
            </span>
            @if($children > 0)
                <span class="badge badge-light-info" title="Children">
                    <x-ui.icon name="profile-user" class="fs-7 me-1" />{{ $children }}
                </span>
            @endif
            @if($infants > 0)
                <span class="badge badge-light-warning" title="Infants">
                    <x-ui.icon name="emoji-happy" class="fs-7 me-1" />{{ $infants }}
                </span>
            @endif
        @endif
    </div>
@else
    <span class="text-muted">—</span>
@endif
