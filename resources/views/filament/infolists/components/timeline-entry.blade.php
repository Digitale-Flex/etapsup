<div class="space-y-4">
    @foreach ($entry->getItems() as $item)
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-0.5 h-6 bg-gray-200"></div>
                <div class="p-2 bg-gray-100 rounded-full">
                    <x-heroicon-o-check class="w-4 h-4 text-gray-500"/>
                </div>
                <div class="w-0.5 h-6 bg-gray-200"></div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                <p class="text-sm text-gray-500">{{ $item['date'] }}</p>
            </div>
        </div>
    @endforeach
</div>
