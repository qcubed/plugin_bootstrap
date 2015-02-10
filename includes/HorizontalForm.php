<?php
/**
 * FormHorizontal class
 * A wrapper class for objects that will be displayed using the RenderFormGroup method, and that will be drawn using
 * the "form-horizontal" class for special styling. This will create an effect of labels next to form objects on
 * larger screens.
 *
 * You should set the PreferredRenderMethod attribute for each of the objects you add to "RenderFormGroup". Using this
 * as a wrapper to objects rendered that way will cause each object to become a Bootstrap row.
 *
 * After adding all the objects to the group, use the helper functions to set up your column breaks.
 *
 * Any objects that do not have a Name attribute will be shifted over to the 2nd column and drawn without a label.
 */
namespace QCubed\Plugin\Bootstrap;


class HorizontalForm extends \QPanel {
	protected $strCssClass = "form-horizontal";
	protected $blnAutoRenderChildren = true;

	/**
	 * Sets the column break for all child controls for a particular device size. Call this after all your controls
	 * have been added to the panel.
	 *
	 * @param $strDeviceSize
	 * @param $intColumns
	 */
	public function SetLabelColumnSize ($strDeviceSize, $intColumns) {
		foreach ($this->GetChildControls() as $objControl) {
			$objControl->SetHorizontalLabelColumnWidth($strDeviceSize, $intColumns);
		}

	}
} 