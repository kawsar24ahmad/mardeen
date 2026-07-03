<div class="flex items-center justify-between p-4 pb-2">
    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
        {{ $heading }}
    </h3>

    <div>
        <select wire:model.live="filter"
            class="block w-full text-sm rounded-lg border-gray-300 bg-white fi-select-input text-gray-950 shadow-sm transition duration-75 focus-within:ring-2 focus-within:ring-primary-600 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus-within:ring-primary-500">
            @foreach($filters as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    </div>
</div>