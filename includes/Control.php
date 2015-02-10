<?php

namespace QCubed\Plugin\Bootstrap;

require_once ('Control.trait.php');

/**
 * Base bootstrap control. Set your QControl to inherit from this control.
 *
 * The implementation passes off most of its functionality to a trait. 2 reasons: You can make a single control
 * into a bootstrap control this way without having to make all your controls have the bootstrap functionality.
 */

//abstract class Control extends \QControlBase {
abstract class Control extends \QControlBase {

	use ControlTrait;	// Pass off most functionality to the trait.


	/**
	 * Note that the constructor cannot be put into the trait, because it will cause a conflict.
	 * @param \QControl|\QControlBase|\QForm $objParent
	 * @param null $strControlId
	 */
	public function __construct ($objParent, $strControlId = null) {
		parent::__construct($objParent, $strControlId);

		Bootstrap::LoadJS($this);

		if ($this instanceof \QTextBoxBase ||
			$this instanceof \QListBox ||
			$this instanceof \QCheckBoxList ||
			$this instanceof \QRadioButtonList) {
			$this->AddCssClass (Bootstrap::FormControl);
		}
	}

}