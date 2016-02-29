<?php


namespace QCubed\Plugin\Bootstrap;

use \QType;

/**
 * Class Button
 * Bootstrap styled buttons
 *
 * @package QCubed\Plugin\Bootstrap
 */
class Button extends \QButton {
	protected $strButtonStyle = Bootstrap::ButtonDefault;
	protected $strCssClass = "btn btn-default";
	protected $strButtonSize = '';
	protected $strGlyph;

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

	public function __set($strName, $mixValue) {
		switch ($strName) {
			case "StyleClass":	// One of Bootstrap::ButtonDefault, ButtonPrimary, ButtonSuccess, ButtonInfo, ButtonWarning, ButtonDanger
				$this->SetStyleClass($mixValue);
				break;

			case "SizeClass": // One of Bootstrap::ButtonLarge, ButtonMedium, ButtonSmall, ButtonExtraSmall
				$this->SetSizeClass($mixValue);
				break;

			case "Glyph": // One of the glyph icons
				$this->strGlyph = QType::Cast ($mixValue, QType::String);
				break;

			case "PrimaryButton":
				try {
					$this->blnPrimaryButton = QType::Cast($mixValue, QType::Boolean);
					$this->SetStyleClass(Bootstrap::ButtonPrimary);
					break;
				} catch (\QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}


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

	protected function GetInnerHtml() {
		$strToReturn = parent::GetInnerHtml();
		if ($this->strGlyph) {
			$strToReturn = sprintf ('<span class="glyphicon %s" aria-hidden="true"></span>', $this->strGlyph) . $strToReturn;
		}
		return $strToReturn;
	}
}