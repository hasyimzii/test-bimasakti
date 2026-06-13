<?php

namespace App\Http\Controllers;

use App\Exceptions\TransactionApiException;
use App\Models\Transaction;
use App\Services\TransactionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionServices $transactionServices,
    ) {}

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
}
