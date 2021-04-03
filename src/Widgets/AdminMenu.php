<?php

namespace Spiderworks\Webadmin\Widgets;

use Arrilot\Widgets\AbstractWidget;
use DB;

class AdminMenu extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $cur_url = \Request::path();
        $parent = DB::table('admin_links')->where('slug', $cur_url)->value('parent_id');
        $menu_items = $this->menu_tree(0);
        return view('spiderworks.webadmin.widgets.admin_menu', [
            'config' => $this->config,
            'menu_items' => $menu_items,
            'parent' => $parent,
            'cur_url' => $cur_url,
        ]);
    }

    public function menu_tree($parent_id)
    {
        $items = DB::table('admin_links')->where('parent_id', $parent_id)->orderBy('display_order', 'ASC')->get();
        if($items)
        {
            foreach ($items as $key => $item) {
                $check_children = DB::table('admin_links')->where('parent_id', $item->id)->count();
                if($check_children>0)
                {
                    $item->children = $this->menu_tree($item->id);
                }
            }
        }
        return $items;
    }
}
