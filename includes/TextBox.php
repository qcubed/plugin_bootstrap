<?php
/**
 * TextBox
 * Text boxes can be parts of input groups (implemented), and can have feedback icons (not yet implemented).
 *
 * Two ways to create a textbox with input groups: Either use this class, or use the InputGroup trait in your base
 * QTextBox class.
 *
 * @property string $SizingClass Bootstrap::InputGroupLarge, Bootstrap::InputGroupMedium or Bootstrap::InputGroupSmall
 * @property string $LeftText Text to appear to the left of the input item.
 * @property string $RightText Text to appear to the right of the input item.
 *

 */

namespace QCubed\Plugin\Bootstrap;

require_once ('InputGroup.trait.php');

use \QType;

class TextBox extends \QTextBox {

	use InputGroupTrait;

	public function __construct ($objParent, $strControlId = null) {
		parent::__construct($objParent, $strControlId);

		Bootstrap::LoadJS($this);

		$this->AddCssClass (Bootstrap::FormControl);
	}


	protected function GetControlHtml() {
		$strToReturn = parent::GetControlHtml();

		return $this->WrapInputGroup($strToReturn);
	}

	public function __get($strName)
	{
		switch ($strName) {
			case "SizingClass": return $this->SizingClass();
			case "LeftText": return $this->LeftText();
			case "RightText": return $this->RightText();
			default:
				try {
					return parent::__get($strName);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}

	public function __set($strName, $mixValue) {
		switch ($strName) {
			case "SizingClass": // Bootstrap::InputGroupLarge, Bootstrap::InputGroupMedium or Bootstrap::InputGroupSmall
				try {
					$this->SetSizingClass($mixValue);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "LeftText":
				try {
					$this->SetLeftText($mixValue);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "RightText":
				try {
					$this->SetRightText($mixValue);
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