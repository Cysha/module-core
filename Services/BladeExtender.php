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
     * Add @menu support
     */
    public function addMenu($value, Application $app, Compiler $blade)
    {
        $matcher = '/@menu\s*\([\'"]([a-zA-Z0-9._-]*)[\'"]\)/';
        return preg_replace($matcher, '<?php echo Menu::handler(\'$1\')->render(); ?> ', $value);
    }

    /**
     * Add @continue & @break support
     */
    public function addContinueBreak($value, Application $app, Compiler $blade)
    {
        $matcher = '/@(break|continue)/';
        return preg_replace($matcher, '<?php $1; ?>', $value);
    }

    /**
     * Add @continue & @break support
     */
    public function setVar($value, Application $app, Compiler $blade)
    {
        $matcher = '/@set\s*\(([a-zA-Z0-9\$\_\[\]\'\"]+), (.+)\)/';
        return preg_replace($matcher, '<?php $1 = $2; ?>', $value);
    }
}
