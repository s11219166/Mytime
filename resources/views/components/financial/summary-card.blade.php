@props(['title', 'amount', 'type', 'icon'])

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
            @switch($icon)
                @case('trending-up')
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    @break
                @case('trending-down')
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                    @break
                @case('piggy-bank')
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @break
                @case('bank')
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4v2l-9 4-9-4V6z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-4 9 4"/>
                    </svg>
                    @break
            @endswitch
            <h3 class="ml-3 text-lg font-medium text-gray-900">{{ $title }}</h3>
        </div>
    </div>
    <div class="amount-field text-3xl font-bold"
         @class([
             'text-green-600' => $type === 'income',
             'text-red-600' => $type === 'expense',
             'text-blue-600' => $type === 'savings',
             'text-purple-600' => $type === 'bank_deposit',
         ])>
        {{ number_format($amount, 2) }}
    </div>
    <div class="mt-2 text-sm text-gray-500">
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            vs last month
        </span>
    </div>
</div>
