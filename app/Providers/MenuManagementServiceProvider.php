<?php

namespace Modules\MenuManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\MenuManagement\Services\MenuService;
use Modules\MenuManagement\Services\PermissionChecker;
use Modules\MenuManagement\Services\MenuCollectorService;

class MenuManagementServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->singleton(PermissionChecker::class, function () {
			return new PermissionChecker();
		});

		$this->app->singleton(MenuCollectorService::class, function ($app) {
			return new MenuCollectorService($app->make(PermissionChecker::class));
		});

		// Register MenuService sebagai singleton
		$this->app->singleton(MenuService::class, function ($app) {
			return new MenuService(
				$app->make(MenuCollectorService::class),
				$app->make(PermissionChecker::class)
			);
		});

		$this->app->bind("menumanagement", function () {
			return $this->app->make(MenuService::class);
		});

		$this->app->register(RouteServiceProvider::class);
	}

	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . "/../../resources/views", "menumanagement");
	}
}
