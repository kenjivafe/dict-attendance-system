@props([
    'timeField',
    'timeValue',
    'title'
])

<div
    x-data
    @class([
        'w-full p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700',
        'cursor-pointer' => $timeValue === 'Not recorded',
        'cursor-not-allowed opacity-75' => $timeValue !== 'Not recorded',
    ])
    @click="$wire.confirmTimeRecord('{{ $timeField }}')"
>
    <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $title }}</h2>
    <div class="text-2xl font-bold {{ $timeValue !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
        {{ $timeValue }}
    </div>
</div>
