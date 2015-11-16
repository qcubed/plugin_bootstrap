<?php
/**
 * InputGroup trait
 *
 * Adds input group functionality to a control. Specifically designed for QTextBox controls and subclasses.
 *
 */

namespace QCubed\Plugin\Bootstrap;

use \QType;

trait InputGroupTrait {
	protected $strSizingClass;
	protected $strLeftText;
	protected $strRightText;
	protected $blnInputGroup = false;	// for subclasses

	/**
	 * Wraps the give code with an input group tag.
	 *
	 * @param $strControlHtml
	 * @return string
	 */
	protected function WrapInputGroup($strControlHtml) {
		if ($this->strLeftText || $this->strRightText || $this->blnInputGroup) {
			$strControlHtml = sprintf ('<div class="input-group %s">%s%s%s</div>',
				$this->strSizingClass,
				$this->GetLeftHtml(),
				$strControlHtml,
				$this->GetRightHtml()
			);
		}

		return $strControlHtml;
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

	protected function SizingClass() {
		return $this->strSizingClass;
	}

	protected function LeftText() {
		return $this->strLeftText;
	}

	protected function RightText() {
		return $this->strRightText;
	}

	abstract public function MarkAsModified();

	protected function SetSizingClass($strSizingClass) {
		$strSizingClass = QType::Cast($strSizingClass, QType::String);
		if ($strSizingClass != $this->strSizingClass) {
			$this->MarkAsModified();
			$this->strSizingClass = $strSizingClass;
		}
	}

	protected function SetLeftText($strLeftText) {
		$strText = QType::Cast($strLeftText, QType::String);
		if ($strText != $this->strLeftText) {
			$this->MarkAsModified();
			$this->strLeftText = $strText;
		}
	}

	protected function SetRightText($strRightText) {
		$strText = QType::Cast($strRightText, QType::String);
		if ($strText != $this->strRightText) {
			$this->MarkAsModified();
			$this->strRightText = $strText;
		}
	}
} 