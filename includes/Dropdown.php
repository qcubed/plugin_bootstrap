<?php

namespace QCubed\Plugin\Bootstrap;

/**
 * A standalone dropdown button. Add ButtonList items to it.
 */

use \QType;

class Dropdown_SelectEvent extends \QEvent {
	const EventName = 'bsdropdownselect';
	const JsReturnParam = 'ui';
}


class Dropdown extends \QHListControl {
	/** @var bool Whether to show it as a button tag or anchor tag */
	protected $blnAsButton = false;
	protected $blnSplit = false;
	protected $blnUp = false;
	protected $strButtonStyle = Bootstrap::ButtonDefault;
	protected $strButtonSize = '';

	public function __construct($objParentObject, $strControlId = null)
	{
		parent::__construct($objParentObject, $strControlId);

		// utilize the wrapper to group the components of the button
		// this also makes sure all events are attached to the link or button itself, rather than the wrapper (not yet sure if this is a good thing.)
		$this->blnUseWrapper = true;
		$this->AddWrapperCssClass("dropdown"); // default to menu type of drowdown
	}

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

	public function AddMenuItem (DropdownItem $objMenuItem) {
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

	public function SetStyleClass($strStyleClass) {
		$this->RemoveCssClass($this->strButtonStyle);
		$this->strButtonStyle = QType::Cast ($strStyleClass, QType::String);
		$this->AddCssClass($this->strButtonStyle);
	}

	public function SetSizeClass($strSizeClass) {
		$this->RemoveCssClass($this->strButtonStyle);
		$this->strButtonSize = QType::Cast ($strSizeClass, QType::String);
		$this->AddCssClass($this->strButtonSize);
	}

	public function GetEndScript() {
		// Trigger the dropdown select event on the main control
		\QApplication::ExecuteSelectorFunction('#' . $this->ControlId . "_list", 'on', 'click', 'li',
			new \QJsClosure("jQuery(this).prev().trigger ('bsdropdownselect', this.id)"), \QJsPriority::High);
		return parent::GetEndScript();
	}

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
					if (!$this->btnSplit) {
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
				if (!$this->btnSplit) {
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

