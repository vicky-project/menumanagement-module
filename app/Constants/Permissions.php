<?php

namespace Modules\MenuManagement\Constants;

class Permissions
{
	const VIEW_MENUS = "menumanagement.view";

	public static function all(): array
	{
		return [
			self::VIEW_MENUS => "View menus",
		];
	}
}
