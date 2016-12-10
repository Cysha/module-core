<?php

namespace Cms\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Cms\Modules\Core;
use Carbon\Carbon;

class NavTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        with(new Core\Models\NavigationLink())->truncate();
        with(new Core\Models\Navigation())->truncate();

        $navs = [
            [
                'name' => 'main-menu',
                'class' => 'nav navbar-nav',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($navs as $r) {
            with(new Core\Models\Navigation())->fill($r)->save();
        }

        $navLinks = [
            [
                'navigation_id' => 1,
                'title' => 'Home',
                'url' => null,
                'route' => 'pxcms.pages.home',
                'class' => null,
                'blank' => 0,
                'order' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ];

        foreach ($navLinks as $r) {
            with(new Core\Models\NavigationLink())->fill($r)->save();
        }
    }
}
