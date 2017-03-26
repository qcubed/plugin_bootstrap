<?php
/**
 * Radio Button List class
 * Bootstrap specific drawing of a QRadioButtonList
 *
 * Modes:
 * 	ButtonModeNone	Display as standard radio buttons using table styling if specified
 *  ButtonModeJq	Display as separate radio buttons styled with bootstrap styling
 *  ButtonModeSet	Display as a button group
 *  ButtonModeList	Display as standard radio buttons with no structure
 *
 * @property-write string ButtonStyle Bootstrap::ButtonPrimary, ButtonSuccess, etc.
 */
namespace QCubed\Plugin\Bootstrap;


class RadioList extends \QRadioButtonList {
	protected $blnWrapLabel = true;
	protected $strButtonGroupClass = "radio";
	protected $strButtonStyle = Bootstrap::ButtonDefault;

	public function __construct($objParentObject, $strControlId = null) {
		parent::__construct($objParentObject, $strControlId);
	}

	/**
	 * Used by drawing routines to render the attributes associated with this control.
	 *
	 * @param null $attributeOverrides
	 * @param null $styleOverrides
	 * @return string
	 */
	public function RenderHtmlAttributes($attributeOverrides = null, $styleOverrides = null)
	{
		if ($this->intButtonMode == \QRadioButtonList::ButtonModeSet) {
			$attributeOverrides["data-toggle"] = "buttons";
			$attributeOverrides["class"] = $this->CssClass;
			\QHtml::AddClass($attributeOverrides["class"], "btn-group");
		}
		return parent::RenderHtmlAttributes($attributeOverrides, $styleOverrides);
	}


	/**
	 * Overrides the radio list get end script to prevent the default JQueryUi functionality.
	 * @return string
	 */
	public function GetEndScript() {
		$strScript = \QListControl::GetEndScript();	// bypass the QRadioButtonList end script
		return $strScript;
	}

	/**
	 * Renders the radio list as a buttonset, rendering just as a list of radio buttons and allowing css or javascript
	 * to format the rest.
	 * @return string
	 */
	public function RenderButtonSet() {
		$count = $this->ItemCount;
		$strToReturn = '';
		for ($intIndex = 0; $intIndex < $count; $intIndex++) {
			$strToReturn .= $this->GetItemHtml($this->GetItem($intIndex), $intIndex, $this->GetHtmlAttribute('tabindex'), $this->blnWrapLabel) . "\n";
		}
		$strToReturn = $this->RenderTag('div', ['id'=>$this->strControlId], null, $strToReturn);
		return $strToReturn;
	}


	/**
	 * Override to fix update control with javascript
	 */
	protected function RefreshSelection() {
		$curIndex = $this->SelectedIndex;
		$count = $this->ItemCount;
		$strToReturn = '';
		for ($intIndex = 0; $intIndex < $count; $intIndex++) {
			\QApplication::ExecuteSelectorFunction(['input', '#' . $this->ControlId . '_' . $intIndex], 'prop', 'checked', $curIndex == $intIndex);
		}
	}

	/**
	 * Adds an active class to the selected item at initial draw time. The bootstrap javascript will do change this
	 * as the user clicks on the various buttons.
	 * 
	 * @param $objItem
	 * @param \QTagStyler $objItemAttributes
	 * @param \QTagStyler $objLabelAttributes
	 */
	protected function OverrideItemAttributes ($objItem, \QTagStyler $objItemAttributes, \QTagStyler $objLabelAttributes) {
		if ($objItem->Selected) {
			$objLabelAttributes->AddCssClass("active");
		}
	}

	public function __set($strName, $mixValue) {
		switch ($strName) {
			// APPEARANCE
			case "ButtonStyle":
				try {
					$this->objItemStyle->RemoveCssClass($this->strButtonStyle);
					$this->strButtonStyle = \QType::Cast($mixValue, \QType::String);
					$this->objItemStyle->AddCssClass($this->strButtonStyle);
					break;
				} catch (\QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "ButtonMode":	// inherited
				try {
					if ($mixValue === self::ButtonModeSet) {
						$this->objItemStyle->SetCssClass("btn");
						$this->objItemStyle->AddCssClass($this->strButtonStyle);
						parent::__set($strName, $mixValue);
						break;
					}
				} catch (\QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			default:
				try {
					parent::__set($strName, $mixValue);
					break;
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}

}