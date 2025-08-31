<div>
    <h1 class="text-lg font-bold mb-2">Working Hours</h1>

    <div class="flex gap-2 mb-4">
        <input type="date" wire:model.live="start_date" class="border rounded px-2 py-1"/>
        <input type="date" wire:model.live="end_date" class="border rounded px-2 py-1"/>
    </div>

    <table class="table-auto border-collapse border border-gray-400 w-full">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-2 py-1">User</th>
                <th class="border px-2 py-1">Total Hours</th>
            </tr>
        </thead>

<tbody>
    @forelse ($col as $item)
        <tr>
            <td class="border px-2 py-1">{{ $item['user'] }}</td>
            <td class="border px-2 py-1 text-right">{{ $item['total_hours'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2" class="text-center py-2">No data</td>
        </tr>
    @endforelse
</tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td class="border px-2 py-1 text-right">Grand Total</td>
                <td class="border px-2 py-1 text-right">{{ $grandTotal }}</td>
            </tr>
        </tfoot>
    </table>
</div>
