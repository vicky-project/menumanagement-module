<?php

use Illuminate\Support\Facades\Route;
use Modules\MenuManagement\Http\Controllers\MenuController;

Route::middleware(["web", "auth"])
	->prefix("admin")
	->name("menumanagement.")
	->group(function () {
		Route::get("menu", MenuController::class)->name("menus");
	});
