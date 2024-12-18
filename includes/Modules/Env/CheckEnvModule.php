<?php

declare(strict_types= 1);


namespace DRO\Pvv\Modules\Env;

use Inpsyde\Modularity\Module\ServiceModule;
// use Inpsyde\Modularity\Module\Module
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Psr\Container\ContainerInterface;
use DRO\Pvv\Modules\Env\CheckEnvService;

class CheckEnvModule implements ServiceModule {

	use ModuleClassNameIdTrait;

	public function services(): array {

		return array(
			CheckEnvService::class => static function ( ContainerInterface $container ): CheckEnvService {
				return new CheckEnvService();
			},
		);
	}
}
