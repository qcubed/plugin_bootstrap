<?php

namespace QCubed\Plugin\Bootstrap;

/**
 * Class MenuButton
 * A button that looks like a menu toggle button for bootstrap. Match this with the QToggleCssClassAction action
 * to turn particular classes on and off. Great for creating custom menus.
 * @package QCubed\Plugin\Bootstrap
 */
class MenuButton extends \QButton {
	protected $strText = 	'<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>';

	protected $blnPrimaryButton = false;

	protected $strCssClass = 'navbar-toggle';

} 