<?php

declare(strict_types= 1);


namespace DRO\Pvv\Modules\Utility;

use Inpsyde\Modularity\Module\ServiceModule;
// use Inpsyde\Modularity\Module\Module
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Psr\Container\ContainerInterface;
use DRO\Pvv\Modules\Utility\HelperService;

class HelperModule implements ServiceModule {

	use ModuleClassNameIdTrait;

	public function services(): array {

		return array(
			HelperService::class => static function ( ContainerInterface $container ): HelperService {
				return new HelperService();
			},
		);
	}
}
