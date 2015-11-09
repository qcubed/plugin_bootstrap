<?php
/**
 * Implement a Bootstrap Pager object.
 *
 **/

namespace QCubed\Plugin\Bootstrap;

include_once("Bootstrap.php");


use \QType, \QApplication, \QHtml, \QPaginatorBase, \QCallerException;

/**
 * Class Pager
 *
 * A simple bootstrap paginator that works more like a pager than a paginator. Shows next and previous arrows, and
 * a page number.
 *
 * We use the pagination class rather than the pager class, because the bootstrap pager has some issues with vertical alignment when
 * using the previous and next classes. The pagination class gives a more pleasing presentation.
 *
 * @package QCubed\Plugin\Bootstrap
 */
class Pager extends \QPaginatorBase {
	/** @var bool Add an arrow to the previous and next buttons */
	protected $blnAddArrow = false;
	/** @var bool Set the buttons to the left and right side of the parent object, vs. bunched in the middle */
	protected $blnSpread = true;


	protected $intSize = 2;

	const Small = 1;
	const Medium = 2;
	const Large = 3;


	public function __construct($objParent, $strControlId = null) {
		parent::__construct($objParent, $strControlId);

		// Default to a very compat format.
		$this->strLabelForPrevious = '&laquo;';
		$this->strLabelForNext = '&raquo;';
	}

	protected function GetPreviousButtonsHtml () {
		$strClasses = "";
		if ($this->blnSpread) {
			$strClasses = "previous";
		}
		$strLabel = $this->strLabelForPrevious;
		if ($this->blnAddArrow) {
			$strLabel = '<span aria-hidden="true">&larr;</span> ' . $strLabel;
		}
		if ($this->intPageNumber <= 1) {
			$strButton = QHtml::RenderTag("a", ["href"=>"#"], $strLabel);
			$strClasses .= " disabled";
		} else {
			$this->mixActionParameter = $this->intPageNumber - 1;
			$strButton = $this->prxPagination->RenderAsLink($strLabel, $this->mixActionParameter, ['id'=>$this->ControlId . "_arrow_" . $this->mixActionParameter], "a", false);
		}

		return QHtml::RenderTag("li", ["class"=>$strClasses], $strButton);
	}

	protected function GetNextButtonsHtml () {
		$strClasses = "";
		if ($this->blnSpread) {
			$strClasses = "next";
		}
		$strLabel = $this->strLabelForNext;
		if ($this->blnAddArrow) {
			$strLabel = $strLabel . ' <span aria-hidden="true">&rarr;</span>' ;
		}
		if ($this->intPageNumber >= $this->PageCount) {
			$strButton = QHtml::RenderTag("a", ["href"=>"#"], $strLabel);
			$strClasses .= " disabled";
		} else {
			$this->mixActionParameter = $this->intPageNumber + 1;
			$strButton = $this->prxPagination->RenderAsLink($strLabel, $this->mixActionParameter, ['id'=>$this->ControlId . "_arrow_" . $this->mixActionParameter], "a", false);
		}

		return QHtml::RenderTag("li", ["class"=>$strClasses], $strButton);
	}

	public function GetControlHtml() {
		$this->objPaginatedControl->DataBind();

		$strPager = $this->GetPreviousButtonsHtml();
		$strLabel = QHtml::RenderTag("a", ["href"=>"#"], $this->intPageNumber . ' ' .  QApplication::Translate("of") . ' ' . $this->PageCount);
		$strPager .= QHtml::RenderTag("li", ["class"=>"disabled"], $strLabel);
		$strPager .= $this->GetNextButtonsHtml();
		$strClass = "pagination";
		if ($this->intSize == self::Small) {
			$strClass .= " pagination-sm";
		} elseif ($this->intSize == self::Large) {
			$strClass .= " pagination-lg";
		}
		$strPager = QHtml::RenderTag("ul", ["class"=>$strClass], $strPager);

		return QHtml::RenderTag("nav", $this->RenderHtmlAttributes(), $strPager);
	}

	public function __get($strName) {
		switch ($strName) {
			case 'AddArrow': return $this->blnAddArrow;
			case 'Spread': return $this->blnSpread;
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
			case 'Spread':
				try {
					$this->blnSpread = QType::Cast($mixValue, QType::Boolean);
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