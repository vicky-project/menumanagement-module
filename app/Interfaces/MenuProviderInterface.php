<?php

namespace Modules\MenuManagement\Interfaces;

interface MenuProviderInterface
{
	/**
	 * Get menu provided by this module.
	 */
	public static function getMenus(): array;
}
