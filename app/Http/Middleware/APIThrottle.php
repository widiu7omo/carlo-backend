<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\InteractsWithTime;
use RuntimeException;

class APIThrottle
{
    use InteractsWithTime;

    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        $key = $prefix.$this->resolveRequestSignature($request);
        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildException($key, $maxAttempts);
        }
        $this->limiter->hit($key, $decayMinutes * 60);
        $response = $next($request);
        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveMaxAttempts($request, $maxAttempts)
    {
        if (Str::contains($maxAttempts, '|')) {
            $maxAttempts = explode('|', $maxAttempts, 2)[$request->user() ? 1 : 0];
        }
        if (! is_numeric($maxAttempts) && $request->user()) {
            $maxAttempts = $request->user()->{$maxAttempts};
        }
        return (int) $maxAttempts;
    }

    protected function resolveRequestSignature($request)
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        }
        if ($route = $request->route()) {
            return sha1($route->getDomain().'|'.$request->ip());
        }
        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    protected function buildException($key, $maxAttempts)
    {
        $message = json_encode(['status' => 0,'message' => 'Too many attempts, please slow down the request.']);
        $response = new Response($message, 429);
        $retryAfter = $this->getTimeUntilNextRetry($key);
        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
    }

    protected function getTimeUntilNextRetry($key)
    {
        return $this->limiter->availableIn($key);
    }

    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter)
        );
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Content-Range, Content-Disposition, Content-Description, X-Auth-Token');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    protected function getHeaders($maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];
        if (! is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }
        return $headers;
    }

    protected function calculateRemainingAttempts($key, $maxAttempts, $retryAfter = null)
    {
        if (is_null($retryAfter)) {
            return $this->limiter->retriesLeft($key, $maxAttempts);
        }
        return 0;
    }
}
