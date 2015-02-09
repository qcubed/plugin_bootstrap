<?php
/**
 * TextBox
 * Text boxes can be parts of input groups (implemented), and can have feedback icons (not yet implemented).
 */
namespace QCubed\Plugin\Bootstrap;

use \QType;

class TextBox extends \QTextBox {
	protected $strSizingClass;
	protected $strLeftText;
	protected $strRightText;
	protected $blnInputGroup = false;	// for subclasses

	protected function GetControlHtml() {
		$strToReturn = parent::GetControlHtml();

		if ($this->strLeftText || $this->strRightText || $this->blnInputGroup) {
			$strToReturn = sprintf ('<div class="input-group %s">%s%s%s</div>',
				$this->strSizingClass,
				$this->GetLeftHtml(),
				$strToReturn,
				$this->GetRightHtml()
			);
		}

		return $strToReturn;
	}

	protected function GetLeftHtml() {
		if ($this->strLeftText) {
			return sprintf ('<span class="input-group-addon">%s</span>', \QApplication::HtmlEntities($this->strLeftText));
		}
		return '';
	}

	protected function GetRightHtml() {
		if ($this->strRightText) {
			return sprintf ('<span class="input-group-addon">%s</span>', \QApplication::HtmlEntities($this->strRightText));
		}
		return '';
	}

	public function __set($strName, $mixValue) {
		switch ($strName) {
			case "SizingClass": // Bootstrap::InputGroupLarge, Bootstrap::InputGroupMedium or Bootstrap::InputGroupSmall
				try {
					$strSizingClass = QType::Cast($mixValue, QType::String);
					if ($strSizingClass != $this->strSizingClass) {
						$this->blnModified = true;
						$this->strSizingClass = $strSizingClass;
					}
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "LeftText":
				try {
					$strText = QType::Cast($mixValue, QType::String);
					if ($strText != $this->strLeftText) {
						$this->blnModified = true;
						$this->strLeftText = $strText;
					}
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "RightText":
				try {
					$strText = QType::Cast($mixValue, QType::String);
					if ($strText != $this->strRightText) {
						$this->blnModified = true;
						$this->strRightText = $strText;
					}
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			default:
				try {
					parent::__set($strName, $mixValue);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;
		}
	}

} 