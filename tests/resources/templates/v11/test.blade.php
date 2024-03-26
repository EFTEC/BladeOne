@php
    $isActive = false;
    $hasError = true;
@endphp
<span @class([
    'p-4',
    'font-bold' => $isActive,
    'text-gray-500' => ! $isActive,
    'bg-red' => $hasError,
])></span>
<span class="p-4 text-gray-500 bg-red"></span>
@php
    $isActive = true;
@endphp
<span @style([
    'background-color: red',
    'font-weight: bold' => $isActive,
])></span>
<span style="background-color: red; font-weight: bold;"></span>
