<?php

namespace Modules\MenuManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MenuManagement\Services\MenuService;
use Modules\MenuManagement\Constants\Permissions;

class MenuController extends Controller
{
	public function __construct()
	{
		$this->middleware(["permission:" . Permissions::VIEW_MENUS]);
	}

	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request, MenuService $menuService)
	{
		$allMenuModules = $menuService->getAllMenus();

		return view("menumanagement::menus.index", compact("allMenuModules"));
	}
}
