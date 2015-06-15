<?php

namespace QCubed\Plugin\Bootstrap;

use \QType, \QApplication, \QHtml;

class Navbar_SelectEvent extends \QEvent {
	const EventName = 'bsmenubarselect';
	const JsReturnParam = 'ui';
}


/**
 * Class Navbar
 * A control that implements a Bootstrap Navbar
 * The "HeaderHtml" attribute will be used as the header text, and the child controls will be used as the
 * "collapse" area. To render an image in the header, set the "HeaderHtml" attribute to the image html.
 *
 * Usage: Create a Navbar object, and add a NavbarList for drop down menus, adding a NavbarItem to the list for each
 * 		  item in the list. You can also add NavbarItems directly to the Navbar object for a link in the navbar.
 */
class Navbar extends \QControl {

	protected $strHeaderAnchor;
	protected $strHeaderText;
	protected $strCssClass = 'navbar navbar-default';


	protected $strStyleClass = 'navbar-default';
	protected $strContainerClass = Bootstrap::ContainerFluid;
	protected $strSelectedId;

	public function __construct ($objParent, $strControlId = null) {
		parent::__construct ($objParent, $strControlId);

		//$this->AddCssFile(__BOOTSTRAP_CSS__);
		Bootstrap::LoadJS($this);
	}

	public function Validate() {return true;}
	public function ParsePostData() {}

	protected function GetControlHtml() {
		$strChildControlHtml = $this->RenderChildren(false);

		$strHeaderText = '';
		if ($this->strHeaderText) {
			$strAnchor = 'href="#"';
			if ($this->strHeaderAnchor) {
				$strAnchor = 'href="' . $this->strHeaderAnchor . '"';
			}
			$strHeaderText = '<a class="navbar-brand" ' . $strAnchor . '>' . $this->strHeaderText . '</a>';
		}

		$strHtml = <<<TMPL
<div class="$this->strContainerClass">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#{$this->strControlId}_collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		 </button>

		$strHeaderText

	</div>
	<div class="collapse navbar-collapse" id="{$this->strControlId}_collapse">
		$strChildControlHtml
	</div>
</div>
TMPL;

		return $this->RenderTag ('nav', ['role'=>'navigation'], null, $strHtml);
	}

	public function GetControlJavaScript() {
		//$strJs = sprintf('jQuery("#%s").%s({%s})', $this->getJqControlId(), $this->getJqSetupFunction(), $this->makeJqOptions());
		$strControlId = $this->ControlId;
		$strJs = <<<JS
			jQuery("#{$strControlId}").on("click", 'li', function() {
                qcubed.recordControlModification("{$strControlId}", "SelectedId", this.id);
                jQuery(this).trigger ('bsmenubarselect', this.id);

            })
JS;
		return $strJs;

	}

	public function GetEndScript() {
		$str = '';
		return $str . $this->GetControlJavaScript() . '; ' . parent::GetEndScript();
	}



	public function __get($strText) {
		switch ($strText) {
			case "ContainerClass": return $this->strContainerClass;
			case "HeaderText": return $this->strHeaderText;
			case "HeaderAnchor": return $this->strHeaderAnchor;
			case "Value":
			case "SelectedId": return $this->strSelectedId;

			default:
				try {
					return parent::__get($strText);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}

	public function __set($strText, $mixValue) {
		switch ($strText) {
			case "ContainerClass":
				try {
					// Bootstrap::ContainerFluid or Bootstrap::Container
					$this->strContainerClass = QType::Cast($mixValue, QType::String);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "HeaderText":
				try {
					$this->strHeaderText = QType::Cast($mixValue, QType::String);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "HeaderAnchor":
				try {
					$this->strHeaderAnchor = QType::Cast($mixValue, QType::String);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "Value":
			case "SelectedId":
				try {
					$this->strSelectedId = QType::Cast($mixValue, QType::String);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "StyleClass":
				try {
					$mixValue = QType::Cast($mixValue, QType::String);
					$this->RemoveCssClass($this->strStyleClass);
					$this->AddCssClass($mixValue);
					$this->strStyleClass = $mixValue;
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}


			default:
				try {
					parent::__set($strText, $mixValue);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;
		}


	}
}

/**
 * Class BsNavbarList
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

/**
 * Class BsNavbarItem
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
		$strHtml = QApplication::HtmlEntities($this->strName);

		if ($strAnchor = $this->strAnchor) {
			$strHtml = QHtml::RenderTag('a', ['href' => $strAnchor], $strHtml, false, true);
		}
		return $strHtml;
	}

	public function GetSubTagAttributes() {
		return null;
	}
}

class NavbarDivider extends NavbarItem {
	protected $strAnchor = ''; // No anchor

	public function __construct() {
		parent::__construct('');
		$this->objItemStyle = new \QListItemStyle();
		$this->objItemStyle->SetCssClass('divider');
	}
}

class NavbarDropdown extends NavbarItem {
	public function __construct($strName) {
		parent::__construct($strName);
		$this->objItemStyle = new \QListItemStyle();
		$this->objItemStyle->SetCssClass('dropdown');
	}

	public function GetItemText() {
		$strHtml = QApplication::HtmlEntities($this->strName);
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

/*
 *
 * Custom Navbar Creation Code for BS v3
 *
 *
 *
 @bgDefault      : #9b59b6;
@bgHighlight    : #8e44ad;
@colDefault     : #ecf0f1;
@colHighlight   : #ecdbff;
.navbar-XXX {
	background-color: @bgDefault;
	border-color: @bgHighlight;
	.navbar-brand {
		color: @colDefault;
		&:hover, &:focus {
			color: @colHighlight; }}
	.navbar-text {
		color: @colDefault; }
	.navbar-nav {
		> li {
			> a {
				color: @colDefault;
				&:hover,  &:focus {
					color: @colHighlight; }}}
		> .active {
			> a, > a:hover, > a:focus {
				color: @colHighlight;
				background-color: @bgHighlight; }}
		> .open {
			> a, > a:hover, > a:focus {
				color: @colHighlight;
				background-color: @bgHighlight; }}}
	.navbar-toggle {
		border-color: @bgHighlight;
		&:hover, &:focus {
			background-color: @bgHighlight; }
		.icon-bar {
			background-color: @colDefault; }}
	.navbar-collapse,
	.navbar-form {
		border-color: @colDefault; }
	.navbar-link {
		color: @colDefault;
		&:hover {
			color: @colHighlight; }}}
@media (max-width: 767px) {
	.navbar-default .navbar-nav .open .dropdown-menu {
		> li > a {
			color: @colDefault;
			&:hover, &:focus {
				color: @colHighlight; }}
		> .active {
			> a, > a:hover, > a:focus, {
				color: @colHighlight;
				background-color: @bgHighlight; }}}
}
 */