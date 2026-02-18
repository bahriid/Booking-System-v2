<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="{{ $dialogClasses() }}">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ $title }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <x-ui.icon name="cross" class="fs-1" />
                </div>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
