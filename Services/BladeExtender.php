<?php namespace Cms\Modules\Core\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\View\Compilers\BladeCompiler as Compiler;

class BladeExtender
{
    /**
     * @param Application $app
     */
    public static function attach(Application $app)
    {
        $blade = view()->getEngineResolver()->resolve('blade')->getCompiler();
        $class = new static;
        foreach (get_class_methods($class) as $method) {
            if ($method == 'attach') continue;

            $blade->extend(function ($value) use ($app, $class, $blade, $method) {
                return $class->$method($value, $app, $blade);
            });
        }
    }

    /**
     * Add @menu('menu_title') support
     */
    public function addMenu($value, Application $app, Compiler $blade)
    {
        $matcher = '/@menu\s*\([\'"]([a-zA-Z0-9._-]*)[\'"]\)/';
        $replace = '<?php echo Menu::handler(\'$1\')->render(); ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @continue & @break support
     */
    public function addContinueBreak($value, Application $app, Compiler $blade)
    {
        $matcher = '/@(break|continue)/';
        $replace = '<?php $1; ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @set($var, 'value') support
     * https://regex101.com/r/uD8bI1/1
     */
    public function setVar($value, Application $app, Compiler $blade)
    {
        $matcher = '/(?<!\w)(\s*)@set\s*\(\s*\${0,1}[\'\"\s]*(.*?)[\'\"\s]*,\s*([\W\w^]*?)\)$/m';
        $replace = '$1<?php \$$2 = $3; ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @debug($var, 'title') support
     * https://regex101.com/r/qX1eH3/7
     */
    public function addDebug($value, Application $app, Compiler $blade)
    {
        // figure out the project root
        $docRoot = (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : null);
        $docRoot = explode('/', $docRoot);
        $docRoot = array_filter($docRoot);
        array_pop($docRoot);
        $docRoot = implode('/', $docRoot);

        // grab the path to current blade file
        $filePath = $blade->getPath();
        $filePath = str_replace('\\', '/', $filePath);

        // replace the project root with the blade path and boom
        $filePath = str_replace(array($docRoot, '/~'), '~', $filePath);

        $matcher = '/@debug\s*\((.*?)(?:,\s+([^)\],]+))?\)$/m';
        $replace = sprintf('<?php echo \Debug::dump($1, \'%s\'); ?>', $filePath);
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @console($var) support
     */
    public function addConsole($value, Application $app, Compiler $blade)
    {
        $matcher = '/@console\s*\((.*?)\)/m';
        $replace = '<?php echo \Debug::console($1); ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @authed support
     */
    public function addAuthed($value, Application $app, Compiler $blade)
    {
        $matcher = '/@authed/';
        $replace = '<?php if (\Auth::check()): ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @authed support
     */
    public function addNotAuthed($value, Application $app, Compiler $blade)
    {
        $matcher = '/@notauthed/';
        $replace = '<?php if (!\Auth::check()): ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @roles('admin', 'user') support
     */
    public function addRoles($value, Application $app, Compiler $blade)
    {
        $matcher = '/@roles\s*\((.*?)\)/';
        $replace = '<?php if (\Auth::check() && \Auth::user()->hasRoles($1)): ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @role('admin') support
     */
    public function addRole($value, Application $app, Compiler $blade)
    {
        $matcher = '/@role\s*\((.*?)\)/';
        $replace = '<?php if (\Auth::check() && \Auth::user()->hasRole($1)): ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add @hasPermission('perm', 'resource'[, 'id']) support
     * Add @permission('perm', 'resource'[, 'id']) support
     */
    public function addHasPermission($value, Application $app, Compiler $blade)
    {
        $matcher = '/@(hasPermission|permission)\s*\((.*?)\)/';
        $replace = '<?php if (hasPermission($2)): ?>';
        return preg_replace($matcher, $replace, $value);
    }

    /**
     * Add a bunch of rules for endif statements
     */
    public function addEndif($value, Application $app, Compiler $blade)
    {
        $matcher = '/@(endroles|endrole|endauthed|endnotauthed|endhaspermission|endpermission)/i';
        $replace = '<?php endif; ?>';
        return preg_replace($matcher, $replace, $value);
    }
}
