<?php

namespace Modules\MenuManagement\Services;

use Nwidart\Modules\Facades\Module;
use Modules\MenuManagement\Interfaces\MenuProviderInterface;

class MenuCollectorService
{
	protected $permissionChecker;

	public function __construct(PermissionChecker $permissionChecker)
	{
		$this->permissionChecker = $permissionChecker;
	}

	/**
	 * Mengumpulkan semua menu dari module yang aktif
	 */
	public function collectMenusFromModules(): array
	{
		$allMenus = [];
		$activeModules = Module::allEnabled();

		foreach ($activeModules as $module) {
			$moduleName = $module->getName();
			$menuProviderClass = "Modules\\{$moduleName}\\Providers\\MenuProvider";

			if (
				class_exists($menuProviderClass) &&
				in_array(
					MenuProviderInterface::class,
					class_implements($menuProviderClass)
				)
			) {
				$moduleMenus = $menuProviderClass::getMenus();

				foreach ($moduleMenus as $menu) {
					$menu["module"] = $moduleName;
					$allMenus[] = $menu;
				}
			}
		}

		// Sort menus by order
		usort($allMenus, function ($a, $b) {
			return $a["order"] <=> $b["order"];
		});

		return $allMenus;
	}

	/**
	 * Get menus for specific user with permission check
	 */
	public function getMenusForUser($user)
	{
		$allMenus = $this->collectMenusFromModules();
		$filteredMenus = [];

		foreach ($allMenus as $menu) {
			$filteredMenu = $this->filterMenuByAccess($menu, $user);
			if ($filteredMenu !== null) {
				$filteredMenus[] = $filteredMenu;
			}
		}

		return $filteredMenus;
	}

	/**
	 * Filter menu based on user permissions
	 */
	protected function filterMenuByAccess($menu, $user)
	{
		// Check if menu has permission requirement
		if (!$this->permissionChecker->canAccessMenu($user, $menu)) {
			return null;
		}

		// Process children recursively
		if (isset($menu["children"]) && is_array($menu["children"])) {
			$filteredChildren = [];

			foreach ($menu["children"] as $child) {
				$filteredChild = $this->filterMenuByAccess($child, $user);
				if ($filteredChild !== null) {
					$filteredChildren[] = $filteredChild;
				}
			}

			// Sort children by order
			usort($filteredChildren, function ($a, $b) {
				return $a["order"] <=> $b["order"];
			});

			$menu["children"] = $filteredChildren;

			// If group has no children after filtering, hide the group
			if (empty($filteredChildren)) {
				return null;
			}
		}

		return $menu;
	}
}
