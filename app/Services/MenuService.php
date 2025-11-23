<?php

namespace Modules\MenuManagement\Services;

class MenuService
{
	protected $menuCollector;
	protected $permissionChecker;

	public function __construct(
		MenuCollectorService $menuCollector,
		PermissionChecker $permissionChecker
	) {
		$this->menuCollector = $menuCollector;
		$this->permissionChecker = $permissionChecker;
	}

	/**
	 * Get menus for current authenticated user
	 */
	public function getMenuForUser($user = null)
	{
		if (!$user && auth()->check()) {
			$user = auth()->user();
		}

		return $this->menuCollector->getMenusForUser($user);
	}

	/**
	 * Get all menus without permission filtering (for admin)
	 */
	public function getAllMenus()
	{
		return $this->menuCollector->collectMenusFromModules();
	}

	/**
	 * Find menu by ID
	 */
	public function findMenuById($menuId)
	{
		$allMenus = $this->getAllMenus();

		foreach ($allMenus as $menu) {
			$found = $this->searchMenuById($menu, $menuId);
			if ($found) {
				return $found;
			}
		}

		return null;
	}

	/**
	 * Recursive search for menu by ID
	 */
	protected function searchMenuById($menu, $menuId)
	{
		if (isset($menu["id"]) && $menu["id"] === $menuId) {
			return $menu;
		}

		if (isset($menu["children"]) && is_array($menu["children"])) {
			foreach ($menu["children"] as $child) {
				$found = $this->searchMenuById($child, $menuId);
				if ($found) {
					return $found;
				}
			}
		}

		return null;
	}
}
