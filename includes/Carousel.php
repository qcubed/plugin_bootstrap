<?php

namespace QCubed\Plugin\Bootstrap;

class Carousel_SelectEvent extends \QEvent {
	const EventName = 'bscarousselect';
	const JsReturnParam = 'ui';
}


/**
 * Class BsCarousel
 * A control that implements a Bootstrap Carousel
 *
 * Use the BsCarousel_SelectEvent to detect a click on an item in the carousel.
 *
 * Note: Keeping track of which carousel item is showing is not currently implemented, mainly because it creates
 * unnecessary traffic between the browser and server, and not sure there is any compelling reason. Also, a redraw of
 * the control will reset the carousel to the first item as active.
 */
class Carousel extends \QHListControl {
	protected $strCssClass = 'carousel slide';

	public function __construct ($objParent, $strControlId = null) {
		parent::__construct ($objParent, $strControlId);

		//$this->AddCssFile(__BOOTSTRAP_CSS__);
	}

	public function Validate() {return true;}
	public function ParsePostData() {}

	protected function GetItemsHtml() {
		$strHtml = '';
		$active = ' active';	// make first one active

		foreach ($this->GetAllItems() as $objItem) {
			$strImg = \QHtml::RenderTag('img', ['class'=>'img-responsive center-block', 'src'=>$objItem->ImageUrl, 'alt'=>$objItem->AltText], null, true);
			if ($objItem->Anchor) {
				$strImg = \QHtml::RenderTag('a', ['href'=>$objItem->Anchor], $strImg);
			}
			$strImg .= \QHtml::RenderTag('div', ['class'=>'carousel-caption'], $objItem->Text);

			$strHtml .= \QHtml::RenderTag('div', ['class'=>'item ' . $active, 'id'=>$objItem->Id], $strImg);
			$active = '';	// subsequent ones are inactive on initial drawing
		}
		return $strHtml;
	}

	protected function GetIndicatorsHtml() {
		$strToReturn = '';
		for ($intIndex = 0; $intIndex < $this->GetItemCount(); $intIndex++) {
			if ($intIndex == 0) {
				$strToReturn .= \QHtml::RenderTag('li', ['data-target'=>'#' . $this->strControlId, 'data-slide-to'=>$intIndex, 'class'=>"active"]);
			} else {
				$strToReturn .= \QHtml::RenderTag('li', ['data-target'=>'#' . $this->strControlId, 'data-slide-to'=>$intIndex]);
			}
		}
		return $strToReturn;
	}

	public function GetControlHtml() {
		$strIndicators = $this->GetIndicatorsHtml();
		$strItems = $this->GetItemsHtml();

		$strHtml = <<<TMPL
<ol class="carousel-indicators">
$strIndicators
</ol>
<div class="carousel-inner" role="listbox">
$strItems
</div>

<a class="left carousel-control" href="#{$this->strControlId}" role="button" data-slide="prev">
	<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	<span class="sr-only">Previous</span>
</a>
<a class="right carousel-control" href="#{$this->strControlId}" role="button" data-slide="next">
	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
	<span class="sr-only">Next</span>
</a>

TMPL;

		return $this->RenderTag('div', ['data-ride'=>'carousel'], null, $strHtml);
	}

	public function GetEndScript() {
		\QApplication::ExecuteControlCommand($this->ControlId, 'on', 'click', '.item',
			new \QJsClosure("jQuery(this).trigger('bscarousselect', this.id)"), \QJsPriority::High);
		return parent::GetEndScript();
	}

}


/**
 * Class BsCarouselItem
 * An item to add to the BsCarousel .
 */
class CarouselItem extends \QHListItem {
	protected $strImageUrl;
	protected $strAltText;

	public function __construct($strImageUrl, $strAltText, $strText = null, $strAnchor = null, $strId = null) {
		parent::__construct($strText, $strAnchor, $strId);
		$this->strImageUrl = $strImageUrl;
		$this->strAltText = $strAltText;
	}

	public function __get($strText) {
		switch ($strText) {
			case "ImageUrl": return $this->strImageUrl;
			case "AltText": return $this->strAltText;

			default:
				try {
					return parent::__get($strText);
				} catch (\QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}

	public function __set($strText, $mixValue) {
		switch ($strText) {
			case "ImageUrl":
				try {
					$this->strImageUrl = \QType::Cast($mixValue, \QType::String);
					break;
				} catch (\QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case "AltText":
				try {
					$this->strAltText = \QType::Cast($mixValue, \QType::String);
					break;
				} catch (\QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			default:
				try {
					parent::__set($strText, $mixValue);
				} catch (\QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;
		}
	}
}