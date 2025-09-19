<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApiJsLogController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'logs' => 'required|array',
                'logs.*.type' => 'required|string',
                'logs.*.message' => 'required|string',
                'logs.*.timestamp' => 'required|string',
                'logs.*.url' => 'required|string',
                'session_id' => 'required|string',
                'page_info' => 'required|array',
            ]);

            foreach ($validated['logs'] as $logEntry) {
                $this->processLogEntry($logEntry, $validated['session_id'], $validated['page_info']);
            }

            return response()->json(['status' => 'success', 'processed' => count($validated['logs'])]);

        } catch (\Exception $e) {
            Log::error('Failed to process JS logs', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Failed to process logs'], 500);
        }
    }

    private function processLogEntry(array $logEntry, string $sessionId, array $pageInfo): void
    {
        $logData = [
            'session_id' => $sessionId,
            'type' => $logEntry['type'],
            'message' => $logEntry['message'],
            'timestamp' => $logEntry['timestamp'],
            'url' => $logEntry['url'],
            'page_info' => $pageInfo,
            'user_agent' => $logEntry['userAgent'] ?? null,
            'stack' => $logEntry['stack'] ?? null,
            'filename' => $logEntry['filename'] ?? null,
            'line' => $logEntry['lineno'] ?? null,
            'column' => $logEntry['colno'] ?? null,
            'data' => $logEntry['data'] ?? null,
        ];

        // Log do standardowego Laravel log
        $logLevel = $this->getLogLevel($logEntry['type']);
        Log::channel('single')->{$logLevel}('JS Error', $logData);

        // Opcjonalnie zapisz do osobnego pliku dla JS errors
        $this->saveToJsLogFile($logData);

        // Dla krytycznych błędów - dodatkowe powiadomienia
        if (in_array($logEntry['type'], ['javascript_error', 'promise_rejection'])) {
            $this->handleCriticalError($logData);
        }
    }

    private function getLogLevel(string $type): string
    {
        return match ($type) {
            'javascript_error', 'promise_rejection' => 'error',
            'console_error' => 'error',
            'console_warn' => 'warning',
            'console_log' => 'info',
            default => 'info'
        };
    }

    private function saveToJsLogFile(array $logData): void
    {
        $logLine = json_encode($logData)."\n";
        $filename = 'js-errors-'.date('Y-m-d').'.log';

        Storage::disk('local')->append('logs/'.$filename, $logLine);
    }

    private function handleCriticalError(array $logData): void
    {
        // Tutaj można dodać:
        // - Wysyłanie emaili dla krytycznych błędów
        // - Integrację z Slack/Discord
        // - Zapis do bazy danych dla dashboard

        Log::channel('single')->critical('Critical JS Error Detected', [
            'session_id' => $logData['session_id'],
            'message' => $logData['message'],
            'url' => $logData['url'],
            'stack' => $logData['stack'],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        // Endpoint do pobierania logów (dla dashboard)
        $date = $request->get('date', date('Y-m-d'));
        $filename = 'logs/js-errors-'.$date.'.log';

        if (! Storage::disk('local')->exists($filename)) {
            return response()->json(['logs' => []]);
        }

        $content = Storage::disk('local')->get($filename);
        $lines = array_filter(explode("\n", $content));

        $logs = array_map(function ($line) {
            return json_decode($line, true);
        }, $lines);

        return response()->json(['logs' => $logs]);
    }
}
