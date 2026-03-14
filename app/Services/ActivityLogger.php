<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


 /**
   * Service de logging
   */
class ActivityLogger
{
  /**
   * Log user activity
   */
  public function logActivity(string $action, string $entity, ?string $entityId = null, ?array $data = null): void
  {
    $context = [
      'user_id' => Auth::id(),
      'user_email' => Auth::user()?->email,
      'action' => $action,
      'entity' => $entity,
      'entity_id' => $entityId,
      'ip' => Request::ip(),
      'user_agent' => Request::userAgent(),
      'timestamp' => now()->toIso8601String(),
    ];

    if ($data) {
      $context['data'] = $data;
    }

    Log::channel('activity')->info("User activity: {$action} on {$entity}", $context);
  }

  /**
   * Log authentication events
   */
  public function logAuth(string $event, ?string $userId = null, ?array $context = []): void
  {
    Log::channel('auth')->info("Auth event: {$event}", array_merge([
      'user_id' => $userId ?? Auth::id(),
      'ip' => Request::ip(),
      'user_agent' => Request::userAgent(),
      'timestamp' => now()->toIso8601String(),
    ], $context));
  }

  /**
   * Log security events
   */
  public function logSecurity(string $event, string $severity = 'warning', ?array $context = []): void
  {
    $logContext = array_merge([
      'user_id' => Auth::id(),
      'ip' => Request::ip(),
      'user_agent' => Request::userAgent(),
      'timestamp' => now()->toIso8601String(),
    ], $context);

    match ($severity) {
      'critical' => Log::channel('security')->critical($event, $logContext),
      'error' => Log::channel('security')->error($event, $logContext),
      'warning' => Log::channel('security')->warning($event, $logContext),
      default => Log::channel('security')->info($event, $logContext),
    };
  }

  /**
   * Log API requests
   */
  public function logApiRequest(string $method, string $endpoint, int $statusCode, ?float $duration = null): void
  {
    Log::channel('api')->info("API Request: {$method} {$endpoint}", [
      'method' => $method,
      'endpoint' => $endpoint,
      'status_code' => $statusCode,
      'duration_ms' => $duration,
      'user_id' => Auth::id(),
      'ip' => Request::ip(),
      'timestamp' => now()->toIso8601String(),
    ]);
  }

  /**
   * Log errors with context
   */
  public function logError(\Throwable $exception, ?string $context = null): void
  {
    Log::channel('errors')->error($exception->getMessage(), [
      'exception' => get_class($exception),
      'message' => $exception->getMessage(),
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
      'trace' => $exception->getTraceAsString(),
      'context' => $context,
      'user_id' => Auth::id(),
      'url' => Request::fullUrl(),
      'method' => Request::method(),
      'timestamp' => now()->toIso8601String(),
    ]);
  }

  /**
   * Log business operations
   */
  public function logOperation(string $operation, string $status, ?array $details = []): void
  {
    Log::channel('operations')->info("Operation: {$operation} - {$status}", array_merge([
      'operation' => $operation,
      'status' => $status,
      'user_id' => Auth::id(),
      'timestamp' => now()->toIso8601String(),
    ], $details));
  }

  /**
   * Log payment transactions
   */
  public function logPayment(string $transactionId, string $status, float $amount, ?array $details = []): void
  {
    Log::channel('payments')->info("Payment: {$transactionId} - {$status}", array_merge([
      'transaction_id' => $transactionId,
      'status' => $status,
      'amount' => $amount,
      'user_id' => Auth::id(),
      'timestamp' => now()->toIso8601String(),
    ], $details));
  }

  /**
   * Log database queries (for debugging)
   */
  public function logQuery(string $sql, array $bindings, float $time): void
  {
    if (config('app.debug')) {
      Log::channel('queries')->debug("Query executed", [
        'sql' => $sql,
        'bindings' => $bindings,
        'time_ms' => $time,
        'timestamp' => now()->toIso8601String(),
      ]);
    }
  }
}
