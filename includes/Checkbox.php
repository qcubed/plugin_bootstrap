<?php
/**
 * Checkbox class
 * Bootstrap specific drawing of a checkbox
 */
namespace QCubed\Plugin\Bootstrap;


class Checkbox extends \QCheckBox {
	protected $blnInline = false;
	protected $blnWrapLabel = true;

	protected function RenderButton ($attrOverride) {
		if (!$this->blnInline) {
			$strHtml = parent::RenderButton($attrOverride);
			return \QHtml::RenderTag('div', ['class'=>'checkbox'], $strHtml);
		}
	}

	protected function renderLabelAttributes() {
		if ($this->blnInline) {
			$this->getCheckLabelStyler()->AddCssClass(Bootstrap::CheckboxInline);
		}
		return parent::renderLabelAttributes();
	}

}