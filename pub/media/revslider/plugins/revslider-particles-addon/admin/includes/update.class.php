<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

use \Nwdthemes\Revslider\Helper\Data;

class RevAddOnParticlesUpdate {
	private $plugin_url		 = 'https://codecanyon.net/item/slider-revolution-responsive-magento-extension/9332896';
	private $remote_url_info = 'addons/revslider-particles-addon/revslider-particles-addon.php';
	private $plugin_slug	 = 'revslider-particles-addon';
	private $plugin_path	 = 'revslider-particles-addon/revslider-particles-addon.php';
	private $version;
	private $plugins;
	private $option;

	protected $_frameworkHelper;

	public function __construct(\Nwdthemes\Revslider\Helper\Framework $frameworkHelper, $version){
		$this->_frameworkHelper = $frameworkHelper;		$this->option = $this->plugin_slug . '_update_info';
		$this->version = $version;
		$this->add_update_checks();
	}


	public function add_update_checks(){
		$this->_frameworkHelper->add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		$this->_frameworkHelper->add_filter('plugins_api', array(&$this, 'set_updates_api_results'), 10, 3);
	}


	public function set_update_transient($transient){
		$this->_check_updates();

		if(isset($transient) && !isset($transient->response)){
			$transient->response = array();
		}

		if(!empty($this->data->basic) && is_object($this->data->basic)){
			if(version_compare($this->version, $this->data->basic->version, '<')){
				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[$this->plugin_path] = $this->data->basic;
			}
		}

		return $transient;
	}


	public function set_updates_api_results($result, $action, $args){
		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->plugin_slug && $action == 'plugin_information'){
			if(is_object($this->data->full) && !empty($this->data->full)){
				$result = $this->data->full;
			}
		}

		return $result;
	}


	protected function _check_updates($force_check = false){
		if((isset(Data::$_GET['checkforupdates']) && Data::$_GET['checkforupdates'] == 'true') || isset(Data::$_GET["force-check"])) $force_check = true;

		//Get data
		if(empty($this->data)){
			$data = $this->_frameworkHelper->get_option($this->option, false);
			$data = $data ? $data : new stdClass;

			$this->data = is_object($data) ? $data : $this->_frameworkHelper->maybe_unserialize($data);
		}

		$last_check = $this->_frameworkHelper->get_option('revslider_particles_addon-update-check');

		if($last_check == false){ //first time called
			$last_check = time();
			$this->_frameworkHelper->update_option('revslider_particles_addon-update-check', $last_check);
		}

		//Check for updates
		if(time() - $last_check > 60 * 60 * 24 * 30 || $force_check == true){
			$data = $this->_retrieve_update_info();

			if(isset($data->basic)){
				$this->_frameworkHelper->update_option('revslider_particles_addon-update-check', time());

				$this->data->checked = time();
				$this->data->basic = $data->basic;
				$this->data->full = $data->full;

				$this->_frameworkHelper->update_option('revslider_particles_addon-latest-version', $data->full->version);
			}
		}

		//Save results
		$this->_frameworkHelper->update_option($this->option, $this->data);
	}


	public function _retrieve_update_info(){
		$rslb = new RevSliderLoadBalancer();
		$data = new stdClass;

		//Build request
		$purchase = ($this->_frameworkHelper->get_option('revslider-valid', 'false') == 'true') ? $this->_frameworkHelper->get_option('revslider-code', '') : '';
		$rattr = array(
			'code' => urlencode($purchase),
			'version' => urlencode($this->version)
		);

		$request = $rslb->call_url($this->remote_url_info, $rattr, 'updates');

		if(!$this->_frameworkHelper->is_wp_error($request)){
			if($response = $this->_frameworkHelper->maybe_unserialize($request['body'])){
				if(is_object($response)){
					$data = $response;

					$data->basic->url = $this->plugin_url;
					$data->full->url = $this->plugin_url;
					$data->full->external = 1;
				}
			}
		}

		return $data;
	}
}
