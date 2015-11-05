<?php
namespace QCubed\Plugin\Bootstrap;

/**
 * Class NavbarItem
 * An item to add to the navbar list.
 */

class NavbarItem extends \QHListItem {
	protected $strAnchor = '#';  // make sure we get a default anchor for attaching clicks

	public function __construct($strText = '', $strValue = null, $strAnchor = null) {
		parent::__construct ($strText, $strValue);
		if ($strAnchor) {
			$this->strAnchor = $strAnchor;
		}
	}

	public function GetItemText() {
		$strHtml = \QApplication::HtmlEntities($this->strName);

		if ($strAnchor = $this->strAnchor) {
			$strHtml = \QHtml::RenderTag('a', ['href' => $strAnchor], $strHtml, false, true);
		}
		return $strHtml;
	}

	public function GetSubTagAttributes() {
		return null;
	}
}
