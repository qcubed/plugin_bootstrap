<?php

namespace QCubed\Plugin\Bootstrap;

/**
 * A standalone dropdown button. Add ButtonList items to it.
 */

use \QType, \QCallerException;

/**
 * Class Dropdown_SelectEvent
 *
 * Use this to respond to the selection of an item from the dropdown list.
 *
 * @package QCubed\Plugin\Bootstrap
 */
class Dropdown_SelectEvent extends \QEvent {
	const EventName = 'bsdropdownselect';
	const JsReturnParam = 'ui';
}

/**
 * Class Dropdown
 *
 * Implements a standalone dropdown button. This can be styled as a typical bootstrap button, with the dropdown list
 * inside the button, or without a button surrounding the list, which makes it look more like a typical menu list.
 *
 * @package QCubed\Plugin\Bootstrap
 *
 * @property string  	$StyleClass		The button style. i.e. Bootstrap::ButtonPrimary
 * @property string  	$SizeClass 		The button size class. i.e. Bootstrap::ButtonSmall
 * @property bool  		$AsButton 		Whether to show it as a button, or a just a menu.
 * @property bool  		$Split 			Whether to split the button into a button and a menu.
 * @property bool  		$Up 			Whether to pop up the menu above or below the button.
 * @property bool  		$Text 			The text to appear on the button. Synonym of Name.
 */
class Dropdown extends \QHListControl {
	/** @var bool Whether to show it as a button tag or anchor tag */
	protected $blnAsButton = false;
	protected $blnSplit = false;
	protected $blnUp = false;
	protected $strButtonStyle = Bootstrap::ButtonDefault;
	protected $strButtonSize = '';

	/**
	 * Dropdown constructor.
	 * @param \QControl|\QControlBase|\QForm $objParentObject
	 * @param string|null $strControlId
	 */
	public function __construct($objParentObject, $strControlId = null)
	{
		parent::__construct($objParentObject, $strControlId);

		// utilize the wrapper to group the components of the button
		$this->blnUseWrapper = true;
		$this->AddWrapperCssClass("dropdown"); // default to menu type of drowdown
	}

	/**
	 * Returns the html for the control.
	 * @return string
	 * @throws \QCallerException
	 */
	public function GetControlHtml() {
		$strHtml = \QHtml::RenderString($this->Name);
		if (!$this->blnAsButton) {
			$strHtml .= ' <span class="caret"></span>';
			$strHtml = $this->RenderTag("a", ["href"=>"#", "data-toggle"=>"dropdown", "aria-haspopup"=>"true", "aria-expanded"=>"false"], null, $strHtml);
		} else {
			if (!$this->blnSplit) {
				$strHtml .= ' <span class="caret"></span>';
				$strHtml = $this->RenderTag("button", ["data-toggle"=>"dropdown", "aria-haspopup"=>"true", "aria-expanded"=>"false"], null, $strHtml);
			} else {
				$strHtml = $this->RenderTag("button", null, null, $strHtml);
				$strClass = "btn dropdown-toggle " . $this->strButtonSize . " " . $this->strButtonStyle;
				$strHtml .= \QHtml::RenderTag("button", ["class" => $strClass, "data-toggle"=>"dropdown", "aria-haspopup"=>"true", "aria-expanded"=>"false"]);
			}
		}
		if ($this->HasDataBinder()) {
			$this->CallDataBinder();
		}
		if ($this->GetItemCount()) {
			$strListHtml = '';
			foreach ($this->GetAllItems() as $objItem) {
				$strListHtml .= $this->GetItemHtml($objItem);
			}

			$strHtml .= \QHtml::RenderTag("ul", ["id"=>$this->ControlId . "_list", "class"=>"dropdown-menu", "aria-labelledby" => $this->ControlId], $strListHtml);
		}
		if ($this->HasDataBinder()) {
			$this->RemoveAllItems();
		}

		return $strHtml;
	}

	/**
	 * Alias to add a dropdown menu item
	 * @param DropdownItem $objMenuItem
	 */
	public function AddMenuItem ($objMenuItem) {
		parent::AddItem ($objMenuItem);
	}

	/**
	 * Return the text html of the item.
	 *
	 * @param DropdownItem $objItem
	 * @return string
	 */
	protected function GetItemText ($objItem) {
		return $objItem->GetItemText();	// redirect to subclasses of item
	}

	/**
	 * Return the attributes for the sub tag that wraps the item tags
	 * @param \QHListItem $objItem
	 * @return array|null|string
	 */
	public function GetSubTagAttributes($objItem) {
		return $objItem->GetSubTagAttributes();
	}

	/**
	 * Set the button style class.
	 * @param $strStyleClass
	 * @throws \QCallerException
	 * @throws \QInvalidCastException
	 */
	public function SetStyleClass($strStyleClass) {
		$this->RemoveCssClass($this->strButtonStyle);
		$this->strButtonStyle = QType::Cast ($strStyleClass, QType::String);
		$this->AddCssClass($this->strButtonStyle);
	}

	/**
	 * Set the button size class.
	 *
	 * @param $strSizeClass
	 * @throws \QCallerException
	 * @throws \QInvalidCastException
	 */
	public function SetSizeClass($strSizeClass) {
		$this->RemoveCssClass($this->strButtonStyle);
		$this->strButtonSize = QType::Cast ($strSizeClass, QType::String);
		$this->AddCssClass($this->strButtonSize);
	}

	/**
	 * Returns the javascript associated with the button.
	 *
	 * @return string
	 * @throws \QCallerException
	 */
	public function GetEndScript() {
		// Trigger the dropdown select event on the main control
		\QApplication::ExecuteSelectorFunction('#' . $this->ControlId . "_list", 'on', 'click', 'li',
			new \QJsClosure("\njQuery('#$this->ControlId').trigger ('bsdropdownselect', {id:this.id, value:\$j(this).data('value')});\n"), \QJsPriority::High);
		return parent::GetEndScript();
	}

	/**
	 * An override to make sure the public value gets decrypted before being sent to the action function.
	 *
	 * @param \QAction $objAction
	 * @param $mixParameter
	 * @return mixed
	 */
	protected function ProcessActionParameters(\QAction $objAction, $mixParameter) {
		$params = parent::ProcessActionParameters($objAction, $mixParameter);
		if ($this->blnEncryptValues) {
			$params['param']['value'] = $this->DecryptValue($mixParameter['value']); // Decrypt the value if needed.
		}
		return $params;
	}

	/**
	 * Magic Get method
	 *
	 * @param string $strName
	 * @return bool|mixed|null|string
	 * @throws \QCallerException
	 * @throws \QCallerException
	 */
	public function __get($strName) {
		switch ($strName) {
			// APPEARANCE
			case "StyleClass": return $this->strButtonStyle;
			case "SizeClass": return $this->strButtonSize;
			case "AsButton": return $this->blnAsButton;
			case "Split": return $this->blnSplit;
			case "Up": return $this->blnUp;
			case "Text": return $this->strName;
			default:
				try {
					return parent::__get($strName);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}


	/**
	 * Magic setter.
	 *
	 * @param string $strName
	 * @param string $mixValue
	 * @throws \QCallerException
	 * @throws \QInvalidCastException
	 */
	public function __set($strName, $mixValue) {
		switch ($strName) {
			case "StyleClass":	// One of Bootstrap::ButtonDefault, ButtonPrimary, ButtonSuccess, ButtonInfo, ButtonWarning, ButtonDanger
				$this->SetStyleClass($mixValue);
				break;

			case "SizeClass": // One of Bootstrap::ButtonLarge, ButtonMedium, ButtonSmall, ButtonExtraSmall
				$this->SetSizeClass($mixValue);
				break;

			case "AsButton":
				$this->blnAsButton = QType::Cast ($mixValue, QType::Boolean);
				if ($this->blnAsButton) {
					$this->AddCssClass("btn");
					$this->AddCssClass($this->strButtonStyle);
					if ($this->strButtonSize) {
						$this->AddCssClass($this->strButtonSize);
					}
					if (!$this->blnSplit) {
						$this->AddCssClass("dropdown-toggle");
					}
					$this->RemoveWrapperCssClass("dropdown");
					$this->AddWrapperCssClass("btn-group");
				} else {
					$this->RemoveCssClass("btn");
					$this->RemoveCssClassesByPrefix("btn-");
					$this->AddWrapperCssClass("dropdown");
					$this->RemoveWrapperCssClass("btn-group");
				}
				break;

			case "Split":
				$this->blnSplit = QType::Cast ($mixValue, QType::Boolean);
				if (!$this->blnSplit) {
					$this->AddCssClass("dropdown-toggle");
				} else {
					$this->RemoveCssClass("dropdown-toggle");
				}

				break;

			case "Up":
				$this->blnUp = QType::Cast ($mixValue, QType::Boolean);
				if ($this->blnUp) {
					$this->AddWrapperCssClass("dropup");
				} else {
					$this->RemoveWrapperCssClass("dropup");
				}
				break;

			case "Text":
				// overload Name as Text too.
				parent::__set("Name", $mixValue);
				break;


			default:
				try {
					parent::__set($strName, $mixValue);
				} catch (\QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;
		}

	}
}

