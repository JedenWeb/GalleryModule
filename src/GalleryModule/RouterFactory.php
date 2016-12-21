<?php

namespace JedenWeb\GalleryModule;

use App\IRouterFactory;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * @author Pavel Jurásek
 */
class RouterFactory implements IRouterFactory
{

	/**
	 * @param string $adminPrefix
	 *
	 * @return array
	 */
	public function create($adminPrefix)
	{
		$router = [];

		$router[IRouterFactory::MODULE_ADMIN] = $adminRouter = new RouteList('Gallery:Admin');

		$adminRouter[] = new Route($adminPrefix.'/galleries[/<action>[/<id>]]', 'Galleries:default');
		$adminRouter[] = new Route($adminPrefix.'/gallery/<action>[/<id>]', 'Gallery:default');

		return $router;
	}

}
