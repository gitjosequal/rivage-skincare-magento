<?php
/*
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/



class RsAddOnParticlesNotice {

	private $title,
			$notice,
			$version,
			$txtDomain,
			$noticeSlug;

	protected $_frameworkHelper;

	public function __construct(
		\Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
		$notice,
		$title,
		$version
	) {
		$this->_frameworkHelper = $frameworkHelper;

		$this->notice = $notice;
		$this->version = $version;
		$this->title = ucfirst($title);
		$this->txtDomain = 'rs_' . $title;
		$this->noticeSlug = 'revslider_' . $title . '_addon';

		$this->_frameworkHelper->add_action('admin_enqueue_scripts', array($this, 'enqueue_notice_script'));
		$this->_frameworkHelper->add_action('admin_notices', array($this, 'add_notice'));

	}

	public function enqueue_notice_script() {

		$this->_frameworkHelper->wp_enqueue_script($this->txtDomain . '-notice', RS_PARTICLES_PLUGIN_URL . 'admin/assets/js/dismiss-admin-notice.js', array('jquery'), $this->version, true);

	}

	/**
	 * Add notice
	 **/
	public function add_notice() {

		switch($this->notice) {

			case 'add_notice_activation':
				$id = md5($this->noticeSlug . '_add_notice_activation');
				$this->notice = 'The <a href="?page=revslider">' . $this->title . ' Add-On</a> requires an active ' .
						   '<a href="//www.themepunch.com/slider-revolution/install-activate-and-update/#register-purchase-code" target="_blank">Purchase Code Registration</a>';
			break;

			case 'add_notice_plugin':
				$id = md5($this->noticeSlug . '_add_notice_activation');
				$this->notice = '<a href="//revolution.themepunch.com/" target="_blank">Slider Revolution</a> required to use the ' . $this->title . ' Add-On';
			break;

			case 'add_notice_version':
				$id = md5($this->noticeSlug . '_add_notice_activation');
				$this->notice = 'The ' . $this->title . ' Add-On requires Slider Revolution ' . RsAddOnParticlesBase::MINIMUM_VERSION .
						   '  <a href="//www.themepunch.com/slider-revolution/install-activate-and-update/#plugin-updates" target="_blank">Update Slider Revolution</a>';
			break;

			default:
				$id = '';
				$this->notice = '';
			// end default

		}

		?>
		<div class="error below-h2 soc-notice-wrap revaddon-notice" style="display: none">
			<p><?php _e($this->notice, $this->txtDomain); ?><span data-addon="<?php echo $this->txtDomain; ?>-notice" data-noticeid="<?php echo $id; ?>" style="float: right; cursor: pointer" class="revaddon-dismiss-notice dashicons dashicons-dismiss"></span></p>
		</div>
		<?php

	}

}
