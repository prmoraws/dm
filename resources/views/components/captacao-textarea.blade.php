@props(['label', 'model', 'rows' => 4])
<label class="block space-y-1.5">
    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $label }}</span>
    <textarea wire:model="{{ $model }}" rows="{{ $rows }}" {{ $attributes->class(['w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white']) }}></textarea>
    @error($model)<span class="block text-xs font-bold text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
</label>

