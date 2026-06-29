@props(['options', 'placeholder' => 'Selecione...'])

<div x-data="{
    open: false,
    search: '',
    options: {{ json_encode($options) }},
    selectedValues: @entangle($attributes->wire('model')),

    get filteredOptions() {
        return this.search === '' ?
            this.options :
            this.options.filter(opt => opt.text.toLowerCase().includes(this.search.toLowerCase()));
    },
    toggleOption(value) {
        this.selectedValues.includes(value) ?
            this.selectedValues = this.selectedValues.filter(v => v !== value) :
            this.selectedValues.push(value);
    }
}" @click.away="open = false" class="relative w-full">

    <div @click="open = !open"
        class="relative w-full flex items-center border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-md shadow-sm p-2 cursor-pointer min-h-[42px]">
        <div class="flex flex-wrap gap-1.5 flex-grow">
            <template x-if="selectedValues.length === 0">
                <span class="text-gray-400 text-sm pl-1">{{ $placeholder }}</span>
            </template>
            <template x-for="option in options.filter(opt => selectedValues.includes(opt.value))" :key="option.value">
                <span
                    class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                    <span x-text="option.text"></span>
                    <button @click.stop="toggleOption(option.value)" type="button"
                        class="flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-blue-600 dark:text-blue-500 hover:bg-blue-200 dark:hover:bg-blue-800/50">
                        <svg class="h-3 w-3" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                            <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                        </svg>
                    </button>
                </span>
            </template>
        </div>
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <div x-show="open" x-transition
        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto"
        style="display: none;">
        <div class="p-2">
            <input x-model.debounce.200ms="search" type="text" placeholder="Buscar..."
                class="w-full px-3 py-2 border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700">
        </div>
        <template x-for="option in filteredOptions" :key="option.value">
            <div @click="toggleOption(option.value)"
                class="relative cursor-pointer select-none py-2 px-3 hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{ 'font-semibold bg-blue-50 dark:bg-blue-900/20': selectedValues.includes(option.value) }">
                <div class="flex items-center justify-between">
                    <span class="block truncate" x-text="option.text"></span>
                    <svg x-show="selectedValues.includes(option.value)" class="h-5 w-5 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </template>
        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500">Nenhuma opção encontrada.
        </div>
    </div>
</div>
