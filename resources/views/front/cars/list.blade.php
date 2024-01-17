<table class="mt-10 w-full">
    <tr>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">№</th>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">Model</th>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">Year</th>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">Color</th>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">Brand</th>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">Available days</th>
        <th class="px-2 shadow-sm ring-1 ring-zinc-200">Total days</th>
    </tr>

    @foreach($cars as $item)
        <tr>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200">{{ $item->car_id  }}</td>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200">{{ $item->name }}</td>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200">{{ $item->year ?? '-' }}</td>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200">{{ $item->color ?? '-' }}</td>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200">{{ $item->brand ?? '-' }}</td>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200 text-center">{{ $item->availableDays ?? '-' }}</td>
            <td class="px-2 shadow-sm ring-1 ring-zinc-200 text-center">{{ $item->days ?? '-' }}</td>
        </tr>
    @endforeach
</table>
