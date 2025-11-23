<?php

namespace Modules\MenuManagement\Services;

use Nwidart\Modules\Facades\Module;

class PermissionChecker
{
	protected $permissionRegistry;

	public function __construct()
	{
		if (
			class_exists(\Modules\UserManagement\Services\PermissionRegistry::class)
		) {
			$this->permissionRegistry = new \Modules\UserManagement\Services\PermissionRegistry();
		}
	}

	/**
	 * Check if user has role (with fallback if UserManagement is disabled)
	 */
	public function userHasRole($user, $roles): bool
	{
		// Jika UserManagement module dinonaktifkan, return true untuk semua role
		if (!$this->isUserManagementEnabled()) {
			return true;
		}

		// Jika roles adalah string, ubah ke array
		$roles = is_string($roles) ? [$roles] : $roles;

		// Jika roles adalah array kosong atau null, return true
		if (empty($roles)) {
			return true;
		}

		// Jika UserManagement aktif, gunakan spatie role check
		try {
			return $user->hasAnyRole($roles);
		} catch (\Exception $e) {
			// Fallback jika ada error
			\Log::warning("Role check failed: " . $e->getMessage());
			return true;
		}
	}

	/**
	 * Check if user has permission (with fallback if UserManagement is disabled)
	 */
	public function userCan($user, $permission): bool
	{
		// Jika UserManagement module dinonaktifkan, return true untuk semua permission
		if (!$this->isUserManagementEnabled()) {
			return true;
		}

		// Jika permission null atau empty, return true
		if (empty($permission)) {
			return true;
		}

		// Jika UserManagement aktif, gunakan spatie permission
		try {
			if (!$this->permissionRegistry) {
				return $user->can($permission);
			}

			if (!$this->permissionRegistry->userCan($user, $permission)) {
				return false;
			}

			return true;
		} catch (\Exception $e) {
			// Fallback jika ada error (misalnya trait HasRoles tidak ada)
			\Log::warning("Permission check failed: " . $e->getMessage());
			return true;
		}
	}

	/**
	 * Check menu access based on role first, then permission
	 */
	public function canAccessMenu($user, $menu): bool
	{
		$hasRole = false;
		$hasPermission = false;

		// Check role requirement first
		if ($isRoleExists = isset($menu["roles"]) && !empty($menu["roles"])) {
			$hasRole = $this->userHasRole($user, $menu["roles"]);
		}

		// Then check permission requirement
		if (
			$isPermissionExists =
				isset($menu["permission"]) && !empty($menu["permission"])
		) {
			$hasPermission = $this->userCan($user, $menu["permission"]);
		}

		return ($isRoleExists ? $hasRole : true) &&
			($isPermissionExists ? $hasPermission : true);
	}

	/**
	 * Check if UserManagement module is available
	 */
	public function isUserManagementEnabled()
	{
		return Module::isEnabled("UserManagement");
	}
}
