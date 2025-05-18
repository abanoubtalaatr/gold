<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class GzipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        try {

            if (method_exists($response, 'content')) {
                $content = $response->content();
                $data = gzencode($content, 9);
                $response->setContent($data);

                $headers = [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET',
                    'Content-type' => 'application/json; charset=utf-8',
                    'Content-Length' => strlen($data),
                    'Content-Encoding' => 'gzip'
                ];

                foreach ($headers as $key => $value) {
                    $response->headers->set($key, $value);
                }
            }
        } catch (BinaryFileResponse $e) {
        }

        return $response;
    }
}
