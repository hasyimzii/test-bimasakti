<tbody id="transactions-tbody" class="divide-y divide-gray-100 bg-white">
    @forelse ($transactions as $transaction)
        <tr class="transition hover:bg-gray-50/50">
            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $transaction->trx_id }}</td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $transaction->provider }}</td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $transaction->product }}</td>
            <td class="whitespace-nowrap px-6 py-4 text-sm">
                @php
                    $statusClasses = match ($transaction->status) {
                        'SUCCESS' => 'bg-green-100 text-green-700',
                        'FAILED' => 'bg-red-100 text-red-700',
                        'PENDING' => 'bg-orange-100 text-orange-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                    {{ $transaction->status }}
                </span>
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No transactions found.</td>
        </tr>
    @endforelse
</tbody>
