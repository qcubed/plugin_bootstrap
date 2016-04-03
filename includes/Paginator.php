<?php
/**
 * Implements a Paginator object using Bootstrap styling.
 *
 **/

namespace QCubed\Plugin\Bootstrap;

include_once("Bootstrap.php");


use \QType, \QApplication, \QHtml, \QPaginatorBase, \QCallerException;

/**
 * Class Paginator
 *
 * A bootstrap implementation of the QCubed paginator.
 *
 * @package QCubed\Plugin\Bootstrap
 */
class Paginator extends \QPaginatorBase {
	/** @var bool Add an arrow to the previous and next buttons */
	protected $blnAddArrow = false;
	protected $strTag = 'nav';

	protected $intSize = 2;

	const Small = 1;
	const Medium = 2;
	const Large = 3;


	public function __construct($objParent, $strControlId = null) {
		parent::__construct($objParent, $strControlId);

		// Default to a very compact format.
		$this->strLabelForPrevious = '&laquo;';
		$this->strLabelForNext = '&raquo;';
	}

	protected function GetPreviousButtonsHtml () {
		list($intPageStart, $intPageEnd) = $this->CalcBunch();

		$strClasses = "";
		$strLabel = $this->strLabelForPrevious;
		if ($this->blnAddArrow) {
			$strLabel = '<span aria-hidden="true">&larr;</span> ' . $strLabel;
		}
		if ($this->intPageNumber <= 1) {
			$strButton = QHtml::RenderTag("span", ["aria-label"=>"Previous"], $strLabel);
			$strClasses .= " disabled";
		} else {
			$this->mixActionParameter = $this->intPageNumber - 1;
			$strButton = $this->prxPagination->RenderAsLink($strLabel, $this->mixActionParameter, ["aria-label"=>"Previous", 'id'=>$this->ControlId . "_arrow_" . $this->mixActionParameter], "a", false);
		}

		$strHtml = QHtml::RenderTag("li", ["class"=>$strClasses], $strButton);

		if ($intPageStart != 1) {
			$strHtml .= $this->GetPageButtonHtml(1);
			$strHtml .= QHtml::RenderTag("li", ["class"=>'disabled'], "<span>&hellip;</span>");
		}
		return $strHtml;

	}

	protected function GetNextButtonsHtml () {
		list($intPageStart, $intPageEnd) = $this->CalcBunch();

		$intPageCount = $this->PageCount;
		$strClasses = "";
		$strLabel = $this->strLabelForNext;
		if ($this->blnAddArrow) {
			$strLabel = $strLabel . ' <span aria-hidden="true">&rarr;</span>' ;
		}
		if ($this->intPageNumber >= $intPageCount) {
			$strButton = QHtml::RenderTag("span", ["aria-label"=>"Next"], $strLabel);
			$strClasses .= " disabled";
		} else {
			$this->mixActionParameter = $this->intPageNumber + 1;
			$strButton = $this->prxPagination->RenderAsLink($strLabel, $this->mixActionParameter, ["aria-label"=>"Next", 'id'=>$this->ControlId . "_arrow_" . $this->mixActionParameter], "a", false);
		}

		$strHtml = QHtml::RenderTag("li", ["class"=>$strClasses], $strButton);

		if ($intPageEnd != $intPageCount) {
			$strHtml = $this->GetPageButtonHtml($intPageCount) . $strHtml;
			$strHtml = QHtml::RenderTag("li", ["class"=>'disabled'], "<span>&hellip;</span>") . $strHtml;
		}

		return $strHtml;
	}

	protected function GetPageButtonHtml ($intIndex) {
		if ($this->intPageNumber == $intIndex) {
			$strToReturn = QHtml::RenderTag("li", ["class"=>"active"], '<span>' . $intIndex . '<span class="sr-only">(current)</span></span>');
		} else {
			$mixActionParameter = $intIndex;
			$strToReturn = $this->prxPagination->RenderAsLink($intIndex, $mixActionParameter, ['id'=>$this->ControlId . "_page_" . $mixActionParameter]);
			$strToReturn = QHtml::RenderTag("li", [], $strToReturn);
		}
		return $strToReturn;
	}


	public function GetControlHtml() {
		$this->objPaginatedControl->DataBind();

		$strToReturn = $this->GetPreviousButtonsHtml();

		list($intPageStart, $intPageEnd) = $this->CalcBunch();

		for ($intIndex = $intPageStart; $intIndex <= $intPageEnd; $intIndex++) {
			$strToReturn .= $this->GetPageButtonHtml($intIndex);
		}

		$strToReturn .= $this->GetNextButtonsHtml();
		$strClass = "pagination";
		if ($this->intSize == self::Small) {
			$strClass .= " pagination-sm";
		} elseif ($this->intSize == self::Large) {
			$strClass .= " pagination-lg";
		}

		$strToReturn = QHtml::RenderTag("ul", ["class"=>$strClass], $strToReturn);

		return QHtml::RenderTag($this->strTag, $this->RenderHtmlAttributes(), $strToReturn);
	}

	public function __get($strName) {
		switch ($strName) {
			case 'AddArrow': return $this->blnAddArrow;
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
			case 'AddArrow':
				try {
					$this->blnAddArrow = QType::Cast($mixValue, QType::Boolean);
					break;
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
			case 'Size':
				try {
					$this->intSize = QType::Cast($mixValue, QType::Integer);
					break;
				} catch (QCallerException $objExc) {
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