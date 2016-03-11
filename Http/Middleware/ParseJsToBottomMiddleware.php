<?php

namespace Cms\Modules\Core\Http\Middleware;

use Closure;

class ParseJsToBottomMiddleware
{
    /**
     * Will finally contain all js tags to move.
     *
     * @var string
     */
    private $jsTags = '';

    /**
     * Contains all exclude regex patterns.
     *
     * @var array
     */
    private $excludeList = array();

    /**
     * Process the JS and move it all down the bottom of the page.
     * This not only speeds up the pages, but allows us to throw js in the partials and it should still work.
     *
     * This has been reworked from a Magento Plugin
     * https://github.com/mediarox/pagespeed/blob/e1df909d03379da1dbe3cb43a4da90e69b24a75d/app/code/community/Pagespeed/Js/Model/Observer.php
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (config('cms.core.app.minify-html', 'false') === 'false') {
        //     return $next($request);
        // }

        // make sure we are a Response and not json etc
        $response = $next($request);
        if ($response instanceof \Illuminate\Http\Response) {
            // get the content into $buffer and check to make sure its a string
            $buffer = $response->getContent();
            if (!is_string($buffer)) {
                return $response;
            }

            if (($closedBodyPosition = strripos($buffer, '</body>')) === false) {
                return $response;
            }

            // Search and replace conditional js units.
            $buffer = preg_replace_callback(
                '#\<\!--\[if[^\>]*>\s*<script.*</script>\s*<\!\[endif\]-->#isU',
                'self::processHit',
                $buffer
            );

            // Search and replace normal js tags.
            $buffer = preg_replace_callback(
                '#<script.*</script>#isU',
                'self::processHit',
                $buffer
            );

            // no JS tags found, throw back the original response
            if (!$this->jsTags) {
                return $response;
            }

            // Remove blank lines from html.
            $buffer = preg_replace('/^\h*\v+/m', '', $buffer);

            // Recalculating </body> position, insert js groups right before body ends and set response.
            $closedBodyPosition = strripos($buffer, '</body>');
            $buffer = substr_replace($buffer, $this->jsTags, $closedBodyPosition, 0);

            //if we end up with null, the string was too big to process so just return the original response
            if ($buffer === null) {
                return $response;
            }

            $response->setContent($buffer);
        }

        return $response;
    }

    /**
     * Processes the matched single js tag or the conditional js tag group.
     *
     * @param array $hits
     *
     * @return string
     */
    public function processHit($hits)
    {
        // Return if hit is blacklisted by exclude list.
        if ($this->isHitExcluded($hits[0])) {
            return $hits[0];
        }

        // Add hit to js tag list and return empty string for the replacement.
        $this->jsTags .= $hits[0]."\n";

        return '';
    }

    /**
     * Is hit on exclude list?
     *
     * @param string $hit
     *
     * @return bool
     */
    protected function isHitExcluded($hit)
    {
        $c = 0;
        preg_replace($this->excludeList, '', $hit, -1, $c);

        return $c > 0;
    }
}
