<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectAgainstSpam
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Basic spam protection checks
        $content = $request->input('content', '');
        $name = $request->input('name', '');

        // Block if too many links
        if (substr_count(strtolower($content), 'http') > 2) {
            return back()->withErrors(['content' => 'Too many links detected.'])->withInput();
        }

        // Block common spam patterns
        $spamPatterns = ['viagra', 'cialis', 'casino', 'lottery', 'prize'];
        foreach ($spamPatterns as $pattern) {
            if (stripos($content, $pattern) !== false || stripos($name, $pattern) !== false) {
                /**
                 * stripos means case-insensitive search, it came from PHP standard library
                 * content here came from the request input field 'content'
                 */
                return back()->withErrors(['content' => 'Spam detected.'])->withInput();
            }
        }

        return $next($request);
    }
}
