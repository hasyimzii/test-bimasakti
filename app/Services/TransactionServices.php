<?php

namespace App\Services;

use App\Exceptions\TransactionApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionServices
{
    private readonly string $baseUrl;
    private readonly string $secret;

    public function __construct(
        ?string $baseUrl = null,
        ?string $secret = null,
    ) {
        $this->baseUrl = rtrim($baseUrl ?? (string) config('services.bimasakti.base_url'), '/');
        $this->secret = $secret ?? (string) config('services.bimasakti.secret');
    }

    // endpoint transaction
    public function getTodayTransactions(): array
    {
        return $this->get('/transactions/today');
    }

    // endpoint provider
    public function getProviders(): array
    {
        return $this->get('/providers', 'Gagal memuat data provider.');
    }

    /**
     * @return array<string, mixed>
     */
    private function get(string $path, string $failureMessage = 'Gagal sinkronisasi data.'): array
    {
        try {
            $response = Http::withHeaders([
                    'X-API-Key' => $this->generateApiKey(),
                ])
                ->timeout(30)
                ->get("{$this->baseUrl}{$path}");
        } catch (ConnectionException $exception) {
            Log::error('Transaction API connection failed', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);

            throw new TransactionApiException($failureMessage);
        }

        if ($response->failed()) {
            throw $this->mapFailedResponse($response);
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['success'] ?? false) !== true) {
            Log::error('Transaction API returned invalid payload', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new TransactionApiException($failureMessage);
        }

        return $payload;
    }

    private function generateApiKey(): string
    {
        return hash('sha256', now()->format('Ymd').$this->secret);
    }

    private function mapFailedResponse(Response $response): TransactionApiException
    {
        $statusCode = $response->status();

        Log::error('Transaction API request failed', [
            'status' => $statusCode,
            'body' => $response->body(),
        ]);

        $userMessage = match ($statusCode) {
            401 => 'Autentikasi gagal. Silakan hubungi administrator.',
            403 => 'Akses ditolak.',
            429 => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
            500 => 'Gagal sinkronisasi data.',
            default => 'Gagal sinkronisasi data.',
        };

        return new TransactionApiException($userMessage, $statusCode);
    }
}
