<?php

namespace Cms\Modules\Core\Http\Middleware;

use Closure;

class MinifyHtmlMiddleware
{
    /**
     * Minify HTML.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->environment() === 'local' || config('cms.core.app.minify-html', 'false') === 'false') {
            return $next($request);
        }

        // make sure we are a Response and not json etc
        $response = $next($request);
        if ($response instanceof \Illuminate\Http\Response) {
            // get the content into $buffer and check to make sure its a string
            $buffer = $response->getContent();
            if (!is_string($buffer)) {
                return $response;
            }

            $re = '%# Collapse whitespace everywhere but in blacklisted elements.
            (?>             # Match all whitespans other than single space.
            [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
            | \s{2,}        # or two or more consecutive-any-whitespace.
            ) # Note: The remaining regex consumes no text at all...
            (?=             # Ensure we are not in a blacklist tag.
            [^<]*+        # Either zero or more non-"<" {normal*}
            (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
            )*+           # Finish "unrolling-the-loop"
            (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
            | \z          # or end of file.
            )             # End alternation group.
            )  # If we made it here, we are not in a blacklist tag.
            %Six';

            // do the replacements
            $buffer = preg_replace($re, ' ', $buffer);

            //if we end up with null, the string was too big to process so just return the orig response
            if ($buffer === null) {
                return $response;
            }

            $response->setContent($buffer);
        }

        return $response;
    }
}
