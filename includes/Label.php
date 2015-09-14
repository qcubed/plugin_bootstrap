<?php
/**
 * Label class
 * Converts a QLabel to be drawn as a bootstrap "Static Control".
 */
namespace QCubed\Plugin\Bootstrap;


class Label extends \QLabel {
	protected $strCssClass = "form-control-static";
	protected $strTagName = "p";
} 