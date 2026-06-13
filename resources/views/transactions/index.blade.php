@extends('layouts.app')

@section('title', 'Transaction Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Transaction Dashboard</h1>
        <button
            id="sync-btn"
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <svg id="sync-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg id="sync-spinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span id="sync-label">Sync Data</span>
        </button>
    </div>

    <div id="sync-alert" class="mb-6 hidden rounded-lg border px-4 py-3 text-sm"></div>

    {{-- Stats Widgets --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Success</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['success']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Failed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['failed']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_amount_formatted'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="mb-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-semibold text-gray-900">Transactions Per Provider</h2>
        <div class="h-72">
            <canvas id="provider-chart"></canvas>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative w-full lg:max-w-sm">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Search Transaction ID"
                        class="w-full rounded-lg border border-gray-200 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <select
                        id="provider-filter"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">All Providers</option>
                        @foreach ($providerOptions as $provider)
                            <option value="{{ $provider }}" @selected(request('provider') === $provider)>{{ $provider }}</option>
                        @endforeach
                    </select>

                    <select
                        id="status-filter"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">All Status</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div id="table-loading" class="hidden border-b border-gray-100 px-6 py-3 text-sm text-gray-500">
            Loading...
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                    </tr>
                </thead>
                @include('transactions.partials.tbody', ['transactions' => $transactions])
            </table>
        </div>

        <div id="transactions-pagination-wrapper">
            @include('transactions.partials.pagination', ['transactions' => $transactions])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const providerChartData = @json($providerChart);

    const ctx = document.getElementById('provider-chart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: providerChartData.map(item => item.provider),
                datasets: [{
                    label: 'Transactions',
                    data: providerChartData.map(item => item.count),
                    backgroundColor: '#2563eb',
                    borderRadius: 4,
                    maxBarThickness: 48,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { size: 12 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { color: '#6b7280', font: { size: 12 } },
                    },
                },
            },
        });
    }

    const syncBtn = document.getElementById('sync-btn');
    const syncIcon = document.getElementById('sync-icon');
    const syncSpinner = document.getElementById('sync-spinner');
    const syncLabel = document.getElementById('sync-label');
    const syncAlert = document.getElementById('sync-alert');

    function showAlert(message, type) {
        syncAlert.textContent = message;
        syncAlert.className = 'mb-6 rounded-lg border px-4 py-3 text-sm ' + (
            type === 'success'
                ? 'border-green-200 bg-green-50 text-green-700'
                : 'border-red-200 bg-red-50 text-red-700'
        );
        syncAlert.classList.remove('hidden');
    }

    syncBtn?.addEventListener('click', async () => {
        syncBtn.disabled = true;
        syncIcon.classList.add('hidden');
        syncSpinner.classList.remove('hidden');
        syncLabel.textContent = 'Syncing...';
        syncAlert.classList.add('hidden');

        try {
            const response = await fetch('/api/transactions/sync', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message || 'Gagal sinkronisasi data.', 'error');
            }
        } catch (error) {
            showAlert('Gagal sinkronisasi data.', 'error');
        } finally {
            syncBtn.disabled = false;
            syncIcon.classList.remove('hidden');
            syncSpinner.classList.add('hidden');
            syncLabel.textContent = 'Sync Data';
        }
    });

    const searchInput = document.getElementById('search-input');
    const providerFilter = document.getElementById('provider-filter');
    const statusFilter = document.getElementById('status-filter');
    const tableLoading = document.getElementById('table-loading');
    const paginationWrapper = document.getElementById('transactions-pagination-wrapper');
    const tableUrl = @json(route('transactions.table'));

    let currentPage = 1;
    let searchTimeout = null;
    let fetchController = null;

    function getFilterParams(page = currentPage) {
        const params = new URLSearchParams();

        if (searchInput?.value.trim()) {
            params.set('search', searchInput.value.trim());
        }

        if (providerFilter?.value) {
            params.set('provider', providerFilter.value);
        }

        if (statusFilter?.value) {
            params.set('status', statusFilter.value);
        }

        params.set('page', page);

        return params;
    }

    function updateBrowserUrl(page = currentPage) {
        const params = getFilterParams(page);
        const query = params.toString();
        const newUrl = query ? `${window.location.pathname}?${query}` : window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }

    async function fetchTransactions(page = 1) {
        currentPage = page;
        fetchController?.abort();
        fetchController = new AbortController();

        tableLoading?.classList.remove('hidden');

        try {
            const response = await fetch(`${tableUrl}?${getFilterParams(page).toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: fetchController.signal,
            });

            if (!response.ok) {
                throw new Error('Failed to fetch transactions');
            }

            const data = await response.json();
            const tbody = document.getElementById('transactions-tbody');

            if (tbody) {
                tbody.outerHTML = data.tbody;
            }

            if (paginationWrapper) {
                paginationWrapper.innerHTML = data.pagination;
            }

            updateBrowserUrl(page);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error(error);
            }
        } finally {
            tableLoading?.classList.add('hidden');
        }
    }

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchTransactions(1), 300);
    });

    providerFilter?.addEventListener('change', () => fetchTransactions(1));
    statusFilter?.addEventListener('change', () => fetchTransactions(1));

    paginationWrapper?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-page]');

        if (!button) {
            return;
        }

        event.preventDefault();
        fetchTransactions(Number(button.dataset.page));
    });
</script>
@endpush
