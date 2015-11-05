<?php

namespace QCubed\Plugin\Bootstrap;

class NavbarDivider extends NavbarItem {
	protected $strAnchor = ''; // No anchor

	public function __construct() {
		parent::__construct('');
		$this->objItemStyle = new \QListItemStyle();
		$this->objItemStyle->SetCssClass('divider');
	}
}
