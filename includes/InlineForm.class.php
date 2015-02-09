<?php
/**
 * FormInline class
 * A wrapper class for objects that will be displayed using the RenderFormGroup method, and that will be drawn using
 * the "form-inline" class for special styling.
 *
 * You should set the PreferredRenderMethod attribute for each of the objects you add.
 *
 * Also, for objects that will be drawn with labels, use the "sr-only" class to hide the labels so that they are
 * available for screen readers.
 */
namespace QCubed\Plugin\Bootstrap;


class InlineForm extends \QPanel {
	protected $strCssClass = "form-inline";
	protected $blnAutoRenderChildren = true;
} 