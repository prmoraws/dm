@props(['titulo', 'descricao' => null])
<div><h2 class="text-xl font-bold text-gray-900">{{ $titulo }}</h2>@if($descricao)<p class="mt-1 text-sm text-gray-600">{{ $descricao }}</p>@endif</div>
