<?php
/**
 * Accordion class
 * A wrapper class for objects that will be displayed using the RenderFormGroup method, and that will be drawn using
 * the "form-inline" class for special styling.
 *
 * You should set the PreferredRenderMethod attribute for each of the objects you add.
 *
 * Also, for objects that will be drawn with labels, use the "sr-only" class to hide the labels so that they are
 * available for screen readers.
 */
namespace QCubed\Plugin\Bootstrap;


class Accordion extends \QDataRepeater {
	const RenderHeader = 'header';
	const RenderBody = 'body';
	const RenderFooter = 'footer';

	protected $strCssClass = Bootstrap::PanelGroup;
	protected $intCurrentOpenItem = 0;
	protected $drawingCallback;

	public function __construct ($objParent, $strControlId = null) {
		parent::__construct ($objParent, $strControlId);

		$this->strTemplate = __DIR__ . '/accordion.tpl.php';
		$this->SetHtmlAttribute("role", "tablist");
		$this->SetHtmlAttribute("aria-multiselectable", "true");
		Bootstrap::LoadJS($this);
	}

	/**
	 * Set the callback that will be used to draw the various parts of the accordion. Callback should take the following
	 * parameters:
	 *  $objAccordion the accordion object
	 *  $strPart, what part of the accordion item is being drawn. See below.
	 *  $objItem the item object from the data source
	 *  $intItemIndex the index of the item being drawn
	 * @param $callable
	 */
	public function SetDrawingCallback ($callable) {
		$this->drawingCallback = $callable;
	}

	/**
	 * Callback from the standard template to render the header html. Calls the callback. The call callback should
	 * call the RenderToggleHelper to render the toggling portion of the header.
	 * @param $objItem
	 */
	protected function RenderHeading ($objItem) {
		if ($this->drawingCallback) {
			call_user_func_array($this->drawingCallback, [$this, self::RenderHeader, $objItem, $this->intCurrentItemIndex]);
		}
	}

	/**
	 * Renders the body of an accordion item. Calls the callback to do so. You have some options here:
	 * 	Draw just text. You should surround your text with <div class="panel-body"></div>
	 *  Draw an item list. You should output a <ul class="list-group"> list (no panel-body needed). See the Bootstrap doc.
	 * @param $objItem
	 */
	protected function RenderBody($objItem) {
		if ($this->drawingCallback) {
			call_user_func_array($this->drawingCallback, [$this, self::RenderBody, $objItem, $this->intCurrentItemIndex]);
		}
	}

	/**
	 * Renders the footer of an accordion item. Calls the callback to do so.
	 * You should surround the content with a <div class="panel-footer"></div>.
	 * If you don't want a footer, do nothing in response to the callback call.
	 * @param $objItem
	 */
	protected function RenderFooter($objItem) {
		if ($this->drawingCallback) {
			call_user_func_array($this->drawingCallback, [$this, self::RenderFooter, $objItem, $this->intCurrentItemIndex]);
		}
	}


	/**
	 * Renders the given html with an anchor wrapper that will make it toggle the currently drawn item. This should be called
	 * from your drawing callback.
	 *
	 * @param $strHtml
	 */
	public function RenderToggleHelper ($strHtml, $blnRenderOutput = true) {
		if ($this->intCurrentItemIndex == $this->intCurrentOpenItem) {
			$strClass = '';
			$strExpanded = 'true';
		} else {
			$strClass = 'collapsed';
			$strExpanded = 'false';
		}
		$strCollapseId = $this->strControlId . '_collapse_' . $this->intCurrentItemIndex;

		$strOut = \QHtml::RenderTag('a',
				['class'=>$strClass,
				'data-toggle'=>'collapse',
				'data-parent'=>'#' . $this->strControlId,
				'href'=>$strCollapseId,
				'aria-expanded'=>$strExpanded,
				'aria-controls'=>$strCollapseId],
				$strHtml, false, true);

		if ($blnRenderOutput) {
			echo $strOut;
		} else {
			return $strOut;
		}

	}

}