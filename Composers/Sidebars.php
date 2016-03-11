<?php

namespace Cms\Modules\Core\Composers;

class Sidebars
{
    public function left($view)
    {
        $view->with('menus', $this->getMenuList(__FUNCTION__));
    }

    public function right($view)
    {
        $view->with('menus', $this->getMenuList(__FUNCTION__));
    }

    private function getMenuList($side)
    {
        $menus = [];

        foreach (get_array_column(config('cms'), 'sidebars.'.$side) as $module => $sets) {
            if (!count($sets)) {
                continue;
            }

            foreach ($sets as $set => $views) {
                $menus[$set] = !empty($menus[$set]) ? array_merge_recursive($menus[$set], $views) : $views;
            }
        }

        foreach ($menus as $set => $views) {
            $this->sortMenu($menus[$set]);
        }

        return $menus;
    }

    private function sortMenu(&$menu)
    {
        usort($menu, function ($a, $b) {
            return array_get($a, 'order', 1) > array_get($b, 'order', 1);
        });
    }
}
