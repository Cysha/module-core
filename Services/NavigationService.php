<?php

namespace Cms\Modules\Core\Services;

use Cms\Modules\Core\Models\Navigation;
use Menu\Menu;

class NavigationService
{
    public function boot()
    {
        // setup the navs from the DB
        $this->setupDBNavs();

        // apply the bootstrap styling to the list navs
        $this->applyListNavs([
            'frontend_user_controlpanel',
        ]);

        $this->stylizeInlineNavs();
    }

    public function setupDBNavs()
    {
        $navigation = (new Navigation())
            ->with('links')
            ->get();

        $callback = function ($children, $item) {
            $url = $item->url;
            if ($item->route !== null) {
                $url = route($item->route);
            }

            $children->add($url, $item->title);
        };

        $navigation->each(function ($nav) use ($callback) {
            Menu::handler($nav->name)
                ->class($nav->class)
                ->hydrate($nav->links, $callback, 'id', 'parent_id');
        });
    }

    public function applyListNavs($menus = [])
    {
        collect($menus)->each(function ($nav) {
            Menu::handler($nav)
                ->addClass('list-group no-style')
                ->getItemsByContentType('Menu\Items\Contents\Link')
                ->map(function ($item) {

                    $class = 'list-group-item';
                    if ($item->isActive()) {
                        $class .= ' active';
                    }

                    $item
                        ->setElement(null)
                        ->getContent()
                        ->addClass($class);
                });
        });
    }

    public function stylizeInlineNavs()
    {
        // grab the inline navs
        $menuKeys = [];
        foreach (get_array_column(config('cms'), 'menus') as $module => $menus) {
            $menuKeys = array_merge($menuKeys, array_keys($menus));
        }
        $menuKeys = array_unique($menuKeys);
        $menuKeys = array_filter($menuKeys, function ($name) {
            return preg_match('/(back|front)end_.*_menu/', $name);
        });

        foreach ($menuKeys as $key) {
            Menu::handler($key)->addClass('nav nav-list');
        }
    }
}
