<?php

namespace App\Http\Controllers;

use App\Exceptions\TransactionApiException;
use App\Models\Transaction;
use App\Services\TransactionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionServices $transactionServices,
    ) {}

    public function index(Request $request): View
    {
        $stats = [
            'total' => Transaction::count(),
            'success' => Transaction::where('status', 'SUCCESS')->count(),
            'failed' => Transaction::where('status', 'FAILED')->count(),
            'total_amount' => (int) Transaction::sum('amount'),
            'total_amount_formatted' => $this->formatAmountShort((int) Transaction::sum('amount')),
        ];

        $providerChart = $this->buildProviderChartData();

        $transactions = $this->paginateTransactions($request);

        $providerOptions = Transaction::query()
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider');

        $statusOptions = ['SUCCESS', 'FAILED', 'PENDING'];

        return view('transactions.index', compact(
            'stats',
            'providerChart',
            'transactions',
            'providerOptions',
            'statusOptions',
        ));
    }

    public function table(Request $request): JsonResponse
    {
        $transactions = $this->paginateTransactions($request);

        return response()->json([
            'tbody' => view('transactions.partials.tbody', compact('transactions'))->render(),
            'pagination' => view('transactions.partials.pagination', compact('transactions'))->render(),
        ]);
    }

    public function syncTransaction(): JsonResponse
    {
        try {
            $response = $this->transactionServices->getTodayTransactions();
            $transactions = $response['data'] ?? [];

            $synced = DB::transaction(function () use ($transactions): int {
                $count = 0;

                foreach ($transactions as $item) {
                    Transaction::updateOrCreate(
                        ['trx_id' => $item['trx_id']],
                        [
                            'provider' => $item['provider'],
                            'product' => $item['product'],
                            'status' => $item['status'],
                            'amount' => $item['amount'],
                            'created_at' => $item['created_at'],
                        ]
                    );

                    $count++;
                }

                return $count;
            });

            return response()->json([
                'success' => true,
                'message' => 'Sinkronisasi data berhasil.',
                'total' => $response['total'] ?? $synced,
                'synced' => $synced,
            ]);
        } catch (TransactionApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->userMessage,
            ], $exception->statusCode ?: 500);
        } catch (Throwable $exception) {
            Log::error('Transaction sync failed', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi data.',
            ], 500);
        }
    }

    private function paginateTransactions(Request $request)
    {
        return Transaction::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('trx_id', 'like', '%'.$request->string('search').'%');
            })
            ->when($request->filled('provider'), function ($query) use ($request) {
                $query->where('provider', $request->string('provider'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * @return list<array{provider: string, count: int}>
     */
    private function buildProviderChartData(): array
    {
        try {
            $response = $this->transactionServices->getProviders();
            $providers = collect($response['data'] ?? [])->pluck('provider');
        } catch (TransactionApiException $exception) {
            Log::warning('Failed to load providers for chart', [
                'message' => $exception->userMessage,
            ]);

            $providers = Transaction::query()
                ->distinct()
                ->orderBy('provider')
                ->pluck('provider');
        }

        $counts = Transaction::query()
            ->select('provider', DB::raw('count(*) as total'))
            ->groupBy('provider')
            ->pluck('total', 'provider');

        return $providers
            ->map(fn (string $provider): array => [
                'provider' => $provider,
                'count' => (int) ($counts[$provider] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function formatAmountShort(int $amount): string
    {
        if ($amount >= 1_000_000_000) {
            return 'Rp '.number_format($amount / 1_000_000_000, 1, '.', '').'B';
        }

        if ($amount >= 1_000_000) {
            return 'Rp '.number_format($amount / 1_000_000, 1, '.', '').'M';
        }

        if ($amount >= 1_000) {
            return 'Rp '.number_format($amount / 1_000, 1, '.', '').'K';
        }

        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
