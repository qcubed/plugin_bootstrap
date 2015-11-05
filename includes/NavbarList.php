<?php

namespace QCubed\Plugin\Bootstrap;

/**
 * Basic Navbar list for inserting into the navbar
 */

class NavbarList extends \QHListControl {
	protected $strCssClass = 'nav navbar-nav';

	public function AddMenuItem (NavbarItem $objMenuItem) {
		parent::AddItem ($objMenuItem);
	}

	/**
	 * Return the text html of the item.
	 *
	 * @param QListItem $objItem
	 * @return string
	 */
	protected function GetItemText (\QHListItem $objItem) {
		return $objItem->GetItemText();	// redirect to subclasses of item
	}

	/**
	 * Return the attributes for the sub tag that wraps the item tags
	 * @param QListItem $objItem
	 * @return null|array|string
	 */
	public function GetSubTagAttributes(\QHListItem $objItem) {
		return $objItem->GetSubTagAttributes();
	}

}

