<?php

namespace QCubed\Plugin\Bootstrap;

class NavbarDropdown extends NavbarItem {
	public function __construct($strName) {
		parent::__construct($strName);
		$this->objItemStyle = new \QListItemStyle();
		$this->objItemStyle->SetCssClass('dropdown');
	}

	public function GetItemText() {
		$strHtml = \QApplication::HtmlEntities($this->strName);
		$strHtml = sprintf ('<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">%s <span class="caret"></span></a>', $strHtml)  . "\n";
		return $strHtml;
	}

	/**
	 * Return the attributes for the sub tag that wraps the item tags
	 * @param QListItem $objItem
	 * @return null|array|string
	 */
	public function GetSubTagAttributes() {
		return ['class'=>'dropdown-menu', 'role'=>'menu'];
	}

}