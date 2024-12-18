<?php

namespace DRO\Pvv\Modules\Utility;

class HelperService {

	public function get_plugin_url() {
		return untrailingslashit( plugins_url( '/', PVV_FILE ) );
	}

	public function get_plugin_path() {
		return untrailingslashit( plugin_dir_path( PVV_FILE ) );
	}

	public function get_plugin_basename() {
		return plugin_basename( PVV_FILE );
	}
}
