<?php

namespace QCubed\Plugin\Bootstrap;

use \QType, \QTagStyler, \QCallerException;

/**
 * Base bootstrap control trait. Th preferred method of adding bootstrap functionality is to make your QControl class
 * inherit from the Control class in Control.class.php. Alternatively you can use this trait to make a control a
 * bootstrap control, but you have to be careful of method collisions. The best way to do this is probably to
 * use it in a derived class of the base class.
 */

trait ControlTrait {

	protected $strValidationState = null;
	/** @var QTagStyler */
	protected $objLabelStyler = null;
	protected $strHorizontalClass = null;


	public function GetLabelStyler() {
		if (!$this->objLabelStyler) {
			$this->objLabelStyler = new \QTagStyler();
			// initialize
			$this->objLabelStyler->AddCssClass('control-label');
		}
		return $this->objLabelStyler;
	}

	public function AddLabelClass($strNewClass) {
		$this->GetLabelStyler()->AddCssClass($strNewClass);
	}

	public function RemoveLabelClass($strCssClassName) {
		$this->GetLabelStyler()->RemoveCssClass($strCssClassName);
	}

	/**
	 * Adds a grid setting to the control.
	 * Generally, you only should do this on QPanel type classes. HTML form object classes drawn with RenderFormGroup
	 * should generally not be given a column class, but rather wrapped in a div with a column class setting. Or, if
	 * you are trying to achieve labels next to control objects, see the directions at FormHorizontal class.
	 *
	 * @param $strDeviceSize
	 * @param int $intColumns
	 * @param int $intOffset
	 * @param int $intPush
	 * @return mixed
	 */
	public function AddColumnClass ($strDeviceSize, $intColumns = 0, $intOffset = 0, $intPush = 0) {
		return ($this->AddCssClass(Bootstrap::CreateColumnClass($strDeviceSize, $intColumns, $intOffset, $intPush)));
	}

	/**
	 * Adds a class to the horizontal column classes, used to define the column breaks when drawing in a
	 * horizontal mode.
	 *
	 * @param $strDeviceSize
	 * @param int $intColumns
	 * @param int $intOffset
	 * @param int $intPush
	 */
	public function AddHorizontalColumnClass ($strDeviceSize, $intColumns = 0, $intOffset = 0, $intPush = 0) {
		$strClass = Bootstrap::CreateColumnClass($strDeviceSize, $intColumns, $intOffset, $intPush);
		$blnChanged = \QHtml::AddClass($this->strHorizontalClass, $strClass);
		if ($blnChanged) {
			$this->MarkAsModified();
		}
	}


	/**
	 * Removes all the column classes for a particular device size from the given class string.
	 * @param $strHaystack
	 * @param string $strDeviceSize Device size to search for. Default will remove all column classes for all device sizes.
	 * @return string New class string with given column classes removed
	 */
	public static function RemoveColumnClasses ($strHaystack, $strDeviceSize = '') {
		$strTest = 'col-' . $strDeviceSize;
		$aRet = array();
		if ($strHaystack) foreach (explode (' ', $strHaystack) as $strClass) {
			if (strpos ($strClass, $strTest) !== 0) {
				$aRet[] = $strClass;
			}
		}
		return implode (' ', $aRet);
	}

	/**
	 * A helper class to quickly set the size of the label part of an object, and the form control part of the object
	 * for a particular device size. Bootstrap is divided into 12 columns, so whatever the label does not take is
	 * left for the control.
	 *
	 * If the contrtol does not have a "Name", we will assume that the label will not be printed, so we will move the
	 * control into the control side of things.
	 *
	 * @param $strDeviceSize
	 * @param $intColumns
	 */
	public function SetHorizontalLabelColumnWidth ($strDeviceSize, $intColumns) {
		$intCtrlCols = 12 - $intColumns;
		if ($this->Name) { // label next to control
			$this->AddLabelClass(Bootstrap::CreateColumnClass($strDeviceSize, $intColumns));
			$this->AddHorizontalColumnClass($strDeviceSize, $intCtrlCols);
		} else { // no label, so shift control to other column
			$this->AddHorizontalColumnClass($strDeviceSize, 0, $intColumns);
			$this->AddHorizontalColumnClass($strDeviceSize, $intCtrlCols);
		}

	}

	/**
	 * Render the control in a form group, with a label, help text and error state.
	 * This is somewhat tricky, as html is not consistent in its structure, and Bootstrap
	 * has different ways of doing things too.
	 *
	 * @param bool $blnDisplayOutput
	 * @throws \Exception
	 * @throws QCallerException
	 */
	public function RenderFormGroup($blnDisplayOutput = true) {
		$this->blnUseWrapper = true;	// always use wrapper, because its part of the form group
		$this->GetWrapperStyler()->AddCssClass('form-group');

		$this->RenderHelper(func_get_args(), __FUNCTION__);

		try {
			$strControlHtml = $this->GetControlHtml();
		} catch (QCallerException $objExc) {
			$objExc->IncrementOffset();
			throw $objExc;
		}

		$blnIncludeFor = false;
		// Try to automatically detect the correct value
		if ($this instanceof \QTextBox ||
			$this instanceof \QListBox ||
			$this instanceof \QCheckBox) {
			$blnIncludeFor = true;
		}

		$strLabel = $this->RenderLabel($blnIncludeFor) . "\n";

		$strHtml = $this->strHtmlBefore . $strControlHtml . $this->strHtmlAfter . $this->GetHelpBlock();

		if ($this->strHorizontalClass) {
			$strHtml = \QHtml::RenderTag('div', ['class' => $this->strHorizontalClass], $strHtml);
		}

		$strHtml = $strLabel . $strHtml;

		return $this->RenderOutput($strHtml, $blnDisplayOutput, true);
	}

	/**
	 * Gets the Label tag contents just before drawing.
	 * Controls that need manual control should override.
	 * If no Name attribute is attached to the control, no label is generated.
	 * @param bool $blnIncludeFor
	 * @return string
	 */
	public function RenderLabel($blnIncludeFor = false) {
		if (!$this->strName) return '';

		$objLabelStyler = $this->GetLabelStyler();
		$attrOverrides['id'] = $this->ControlId . '_label';

		if ($blnIncludeFor) {
			$attrOverrides['for'] = $this->ControlId;
		}

		return \QHtml::RenderTag('label', $objLabelStyler->RenderHtmlAttributes($attrOverrides), \QApplication::HtmlEntities($this->strName), false, true);
	}

	protected function GetHelpBlock() {
		$strHtml = "";
		if ($this->strValidationError) {
			$strHtml .= \QHtml::RenderTag('p', ['class'=>'help-block', 'id'=>$this->strControlId . '_error'], $this->strValidationError);
		}
		elseif ($this->strWarning) {
			$strHtml .= \QHtml::RenderTag('p', ['class'=>'help-block', 'id'=>$this->strControlId . '_warning'], $this->strWarning);
		}
		elseif ($this->strInstructions) {
			$strHtml .= \QHtml::RenderTag('p', ['class'=>'help-block', 'id'=>$this->strControlId . '_help'], $this->strInstructions);
		}
		return $strHtml;
	}


	/**
	 * Returns the attributes for the control.
	 * @param bool $blnIncludeCustom
	 * @param bool $blnIncludeAction
	 * @return string
	 */
	public function RenderHtmlAttributes($attributeOverrides = null, $styleOverrides = null) {
		if ($this->strValidationError) {
			$attributeOverrides['aria-describedby'] = $this->strControlId . '_error';
		}
		elseif ($this->strWarning) {
			$attributeOverrides['aria-describedby'] = $this->strControlId . '_warning';
		}
		elseif ($this->strInstructions) {
			$attributeOverrides['aria-describedby'] = $this->strControlId . '_help';
		}

		return parent::RenderHtmlAttributes($attributeOverrides, $styleOverrides);
	}

	public function ValidationReset() {
		if ($this->strValidationState) {
			$this->strValidationState = null;
			$this->RemoveWrapperCssClass($this->strValidationState);
		}
		parent::ValidationReset();
	}

	/**
	 * Since wrappers do not redraw on a validation error (only their contents redraw), we must use javascript to change
	 * the wrapper class. Which is fine, since its faster.
	 */
	public function ReinforceValidationState() {
		// TODO: Classes that don't use a wrapper
		if ($this->strValidationError) {
			$this->AddWrapperCssClass(Bootstrap::HasError);
			$this->strValidationState = Bootstrap::HasError;
		}
		elseif ($this->strWarning) {
			$this->AddWrapperCssClass(Bootstrap::HasWarning);
			$this->strValidationState = Bootstrap::HasWarning;
		}
		else {
			$this->AddWrapperCssClass(Bootstrap::HasSuccess);
			$this->strValidationState = Bootstrap::HasSuccess;
		}
	}


	public function ValidateControlAndChildren() {
		// Initially Assume Validation is True
		$blnToReturn = parent::ValidateControlAndChildren();
		/*
				// Check the Control Itself
				if (!$blnToReturn) {
					foreach ($this->GetChildControls() as $objChildControl) {
						$objChildControl->ReinforceValidationState();
					}
				}*/
		$this->ReinforceValidationState();
		return $blnToReturn;
	}

	/**
	 * The Superclass assumes that we want everything to be inline. We essentially turn this off to allow Bootstrap
	 * to do what it wants.
	 *
	 * @param bool $blnIsBlockElement
	 * @return string
	 */
	protected function GetWrapperStyleAttributes($blnIsBlockElement=false) {
		return '';
	}

	// Abstract classes to squash warnings
	abstract public function MarkAsModified();
	abstract public function AddCssClass($strClass);
	abstract public function GetWrapperStyler();

	public function __set($strName, $mixValue) {
		switch ($strName) {
			case 'ValidationError':
				parent::__set($strName, $mixValue);
				$this->ReinforceValidationState();
				break;

			case 'Warning':
				parent::__set($strName, $mixValue);
				$this->ReinforceValidationState();
				break;

			case "Display":
				parent::__set($strName, $mixValue);
				if ($this->blnUseWrapper) {
					if ($this->blnDisplay) {
						$this->RemoveWrapperCssClass(Bootstrap::Hidden);
					} else {
						$this->AddWrapperCssClass(Bootstrap::Hidden);
					}
				} else {
					if ($this->blnDisplay) {
						$this->RemoveCssClass(Bootstrap::Hidden);
					} else {
						$this->AddCssClass(Bootstrap::Hidden);
					}
				}
				break;

			case "LabelCssClass":
				$strClass = QType::Cast ($mixValue, QType::String);
				$this->GetLabelStyler()->SetCssClass($strClass);
				break;

			case "HorizontalClass": // for wrapping a control with a div with this class, mainly for column control on horizontal forms
				$this->strHorizontalClass = QType::Cast ($mixValue, QType::String);
				break;

			default:
				try {
					parent::__set($strName, $mixValue);
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;
		}

	}

}