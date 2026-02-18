<i class="ki-duotone ki-{{ $name }} {{ $class }}">
    @for ($i = 1; $i <= $pathCount(); $i++)
        <span class="path{{ $i }}"></span>
    @endfor
</i>
