<div class="d-flex align-items-center">
    <div class="symbol {{ $sizeClass() }}">
        @if($image)
            <img src="{{ $image }}" alt="{{ $name }}" class="rounded">
        @else
            <div class="symbol-label fs-5 fw-bold bg-light-primary text-primary rounded">
                {{ $initials() }}
            </div>
        @endif
    </div>
    @if($showName)
        <div class="ms-3">
            <span class="text-gray-800 fw-bold">{{ $name }}</span>
        </div>
    @endif
</div>
