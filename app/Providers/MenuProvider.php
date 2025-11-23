<?php

namespace Modules\MenuManagement\Providers;

use Modules\MenuManagement\Constants\Permissions;
use Modules\MenuManagement\Interfaces\MenuProviderInterface;

class MenuProvider implements MenuProviderInterface
{
	public static function getMenus(): array
	{
		return [
			[
				"id" => "menu-management",
				"name" => "Menu Management",
				"icon" => "list-rich",
				"order" => 2,
				"role" => ["super-admin", "admin"],
				"route" => "menumanagement.menus",
				"permission" => Permissions::VIEW_MENUS,
			],
		];
	}
}
