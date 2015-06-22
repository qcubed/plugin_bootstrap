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

	public function __set($strName, $mixValue) {
		switch ($strName) {
			case "StyleClass":	// One of Bootstrap::ButtonDefault, ButtonPrimary, ButtonSuccess, ButtonInfo, ButtonWarning, ButtonDanger
				$this->RemoveCssClass($this->strButtonStyle);
				$this->strButtonStyle = QType::Cast ($mixValue, QType::String);
				$this->AddCssClass($this->strButtonStyle);
				break;

			case "SizeClass": // One of Bootstrap::ButtonLarge, ButtonMedium, ButtonSmall, ButtonExtraSmall
				$this->RemoveCssClass($this->strButtonSize);
				$this->strButtonSize = QType::Cast ($mixValue, QType::String);
				$this->AddCssClass($this->strButtonSize);
				break;

			case "Glyph": // One of the glyph icons
				$this->strGlyph = QType::Cast ($mixValue, QType::String);
				break;

			case "PrimaryButton":
				try {
					$this->blnPrimaryButton = QType::Cast($mixValue, QType::Boolean);
					$this->StyleClass = Bootstrap::ButtonPrimary;
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}


			default:
				try {
					parent::__set($strName, $mixValue);
				} catch (QInvalidCastException $objExc) {
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