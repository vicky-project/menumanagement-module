<?php

namespace Modules\MenuManagement\Providers;

use Modules\MenuManagement\Interfaces\MenuProviderInterface;

class BaseMenuProvider implements MenuProviderInterface
{
	/**
	 * Default structur menu validation.
	 */
	protected static function validate(array $menu): void
	{
		$required = ["id", "name", "order"];

		foreach ($required as $field) {
			if (!isset($menu[$field])) {
				throw new \InvalidArgumentException(
					"Menu item is missing required field: {$field}"
				);
			}
		}
	}

	/**
	 * Generate unique menu ID.
	 */
	protected static function generateId(string $module, string $id): string
	{
		return "{$module}.{$id}";
	}
}
