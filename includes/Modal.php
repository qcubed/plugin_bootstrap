<?php
/**
 * Modal Class
 *
 * The Modal class defined here provides the dialog functionality of bootstrap modals in a way that is accessible
 * from QCubed.
 *
 * The interface is similar to the QDialog interface. This is a subclass of QPanel, so you can define whatever
 * you want to appear in a QPanel, and it will show up in the modal. There are also functions to add buttons at
 * the bottom, a title and close button at the top, and to respond to those clicks.
 *
 * This current implementation uses javascript to wrap the panel in bootstrap friendly html, similar to how
 * jQueryUI works.
 *
 * Currently, bootstrap does not support multiple modals up at once (stacked modals), though this could be done
 * in the javascript.
 *
 */

namespace QCubed\Plugin\Bootstrap;

use \QEvent, \QDialog, \QApplication, \QPanel, \QJsPriority, \QType, \QInvalidCastException, \QCallerException;

/**
 * Class Modal_ShowEvent
 * Captures the modal show event, which happens before a modal is shown.
 * Param will be the id of the button that was clicked to show the triangle, if that
 * button was tied to the dialog using a data-toggle="modal" attribute.
 *
 * @package QCubed\Plugin\Bootstrap
 */
class Modal_ShowEvent extends QEvent {
	/** Event Name */
	const EventName = 'show.bs.modal';
	const JsReturnParam = 'event.relatedTarget.id';
}

/**
 * Class Modal_ShownEvent
 * Captures the modal shown event, which happens after modal is shown.
 * Param will be the id of the button that was clicked to show the triangle, if that
 * button was tied to the dialog using a data-toggle="modal" attribute.
 * @package QCubed\Plugin\Bootstrap
 */
class Modal_ShownEvent extends QEvent {
	/** Event Name */
	const EventName = 'shown.bs.modal';
	const JsReturnParam = 'event.relatedTarget.id';
}

/**
 * Class Modal_HideEvent
 * Captures the modal hide event, which happens before the dialog is hidden.
 * @package QCubed\Plugin\Bootstrap
 */
class Modal_HideEvent extends QEvent {
	/** Event Name */
	const EventName = 'hide.bs.modal';
}

/**
 * Class Modal_HiddenEvent
 * Captures the modal hidden event, which happens after the dialog is hidden.
 * @package QCubed\Plugin\Bootstrap
 */
class Modal_HiddenEvent extends QEvent {
	/** Event Name */
	const EventName = 'hidden.bs.modal';
}


/**
 * Implements a Bootstrap modal dialog
 *
 * There are a couple of ways to use the dialog. The simplest is as follows:
 *
 * In your Form_Create():
 * <code>
 * $this->dlg = new BS\Modal($this);
 * $this->dlg->Text = 'Show this on the dialog.'
 * $this->dlg->AddButton ('OK', 'ok', false, true, null, ['data-dismiss'='modal']);
 * </code>
 *
 * When you want to show the modal:
 * <code>
 * $this->dlg->ShowDialogBox();
 * </code>
 *
 * You do not need to draw the dialog. It will automatically be drawn for you.
 *
 * Since Modal is a descendant of QPanel, you can do anything you can to a normal QPanel,
 * including add QControls and use a template. When you want to hide the dialog, call <code>HideDialogBox()</code>
 *
 * However, do not mark the dialog's wrapper as modified while it is being shown. This will cause redraw problems.
 *
 * @property boolean $AutoOpen Automatically opens the dialog when its drawn.
 * @property boolean $Show Synonym of AutoOpen.
 * @property boolean $HasCloseButton Disables (false) or enables (true) the close X in the upper right corner of the title.
 * 	Can be set when initializing the dialog. Also enables or disables the ability to close the box by pressing the ESC key.
 * @property boolean $CloseOnEscape Allows the ESC key to automatically close the dialog with no button click.
 * @property boolean $Keyboard Synonym of CloseOnEscape.
 * @property boolean $Fade Whether to fade in (default), or just make dialog appear instantly.
 * @property string $Title Title to display at the top of the dialog.
 * @property string $Size Bootstrap::ModalLarge or Bootstrap::ModalSmall.
 * @property mixed $Backdrop true to use grayed out backdrop (default), false to not have a backdrop, and the word "static" to have a backdrop and not allow clicking outside of the dialog to dismiss.
 * @property string $HeaderClasses Additional classes to add to the header. Useful for giving a header background, like Bootstrap::BackgroundWarning
 * @property-read integer $ClickedButton Returns the id of the button most recently clicked. (read-only)
 * @property-write string $DialogState Set whether this dialog is in an error or highlight (info) state. Choose on of QDialog::StateNone, QDialogState::StateError, QDialogState::StateHighlight (write-only)
 *
 * @link http://getbootstrap.com/javascript/#modals
 * @package QCubed\Plugin\Bootstrap
 */
	
class Modal extends QPanel
{
	/** @var bool make sure the modal gets rendered */
	protected $blnAutoRender = true;

	/** The control id to use for the reusable global alert dialog. */
	const MessageDialogId = 'qAlertDialog';

	/** @var bool default to auto open being false, since this would be a rare need, and dialogs are auto-rendered. */
	protected $blnAutoOpen = false;
	/** @var  string Id of last button clicked. */
	protected $strClickedButtonId;
	/** @var bool Should we draw a close button on the top? */
	protected $blnHasCloseButton = true;
	/** @var bool records whether dialog is open */
	protected $blnIsOpen = false;
	/** @var array whether a button causes validation */
	protected $blnValidationArray = array();

	protected $blnUseWrapper = true;
	/** @var  string state of the dialog for special display */
	protected $strDialogState;
	/** @var  string */
	protected $strHeaderClasses;
	/** @var  bool */
	protected $blnCloseOnEscape;
	/** @var  bool */
	protected $blnFade;
	/** @var  string */
	protected $strTitle;
	/** @var  array */
	protected $mixButtons;
	/** @var  Bootstrap::ModalLarge or ModalSmall */
	protected $strSize;
	/** @var  bool|string true or false whether to have an overlay backdrop, or the string "static", which means have a backdrop, and don't close when clicking outside of dialog. */
	protected $mixBackdrop;

	/**
	 * Modal constructor.
	 * @param \QControlBase|\QForm $objParentObject
	 * @param string|null $strControlId
	 */
	public function __construct($objParentObject, $strControlId = null) {
		parent::__construct($objParentObject, $strControlId);
		$this->mixCausesValidation = $this;
		Bootstrap::LoadJS($this);
		$this->AddPluginCssFile($this, __BOOTSTRAP_CSS__);
		$this->AddPluginJavascriptFile('bootstrap', 'qc.bs.modal.js');

		/* Setup wrapper to prevent flash drawing of unstyled dialog. */
		$objWrapperStyler = $this->GetWrapperStyler();
		$objWrapperStyler->AddCssClass('modal fade');
		$objWrapperStyler->SetHtmlAttribute('tabIndex', -1);
		$objWrapperStyler->SetHtmlAttribute('role', 'dialog');
	}

	/**
	 * Validate the child items if the dialog is visible and the clicked button requires validation.
	 * This piece of magic makes validation specific to the dialog if an action is coming from the dialog,
	 * and prevents the controls in the dialog from being validated if the action is coming from outside
	 * the dialog.
	 *
	 * @return bool
	 */
	public function ValidateControlAndChildren() {
		if ($this->blnIsOpen) {	// don't validate a closed dialog
			if (!empty($this->mixButtons)) {	// using built-in dialog buttons
				if (!empty ($this->blnValidationArray[$this->strClickedButtonId])) {
					return parent::ValidateControlAndChildren();
				}
			} else {	// using QButtons placed in the control
				return parent::ValidateControlAndChildren();
			}
		}
		return true;
	}

	/**
	 * @return string
	 */
	protected function GetJqSetupFunction() {
		return 'bsModal';
	}

	/**
	 * Returns the control id for purposes of attaching events.
	 * @return string
	 */
	public function GetJqControlId() {
		return $this->ControlId . '_ctl';
	}

	/**
	 * Overrides the parent to call the qc.bs.modal js initializer.
	 *
	 * @return string
	 */

	public function GetEndScript() {
		QApplication::ExecuteControlCommand($this->GetJqControlId(), "off", QJsPriority::High);
		$jqOptions = $this->MakeJqOptions();
		QApplication::ExecuteControlCommand($this->ControlId, $this->GetJqSetupFunction(), $jqOptions, QJsPriority::High);

		return parent::GetEndScript();
	}

	/**
	 * Returns an array of options that get set to the setup function as javascript.
	 * @return null
	 */
	protected function MakeJqOptions() {
		$jqOptions = null;
		if (!is_null($val = $this->AutoOpen)) {$jqOptions['show'] = $val;}
		if (!is_null($val = $this->CloseOnEscape)) {$jqOptions['keyboard'] = $val;}
		if (!is_null($val = $this->Backdrop)) {$jqOptions['backdrop'] = $val;}
		if (!is_null($val = $this->Fade)) {$jqOptions['fade'] = $val;}
		if (!is_null($val = $this->Title)) {$jqOptions['title'] = $val;}
		if (!is_null($val = $this->Size)) {$jqOptions['size'] = $val;}

		if (!is_null($this->mixButtons)) {$jqOptions['buttons'] = $this->mixButtons;}

		switch ($this->strDialogState) {
			case QDialog::StateError:
				$strHeaderClasses = Bootstrap::BackgroundDanger;
				break;

			case QDialog::StateHighlight:
				$strHeaderClasses = Bootstrap::BackgroundWarning;
				break;

			default:
				if ($this->strHeaderClasses) {
					$strHeaderClasses = $this->strHeaderClasses;
				} else {
					$strHeaderClasses = Bootstrap::BackgroundPrimary;
				}
		}

		$jqOptions['headerClasses'] = $strHeaderClasses;

		return $jqOptions;
	}


	/**
	 * Adds a button to the dialog. Use this to add buttons BEFORE bringing up the dialog.
	 *
	 * @param string $strButtonName		  Text to use on the button.
	 * @param string $strButtonId         Id associated with the button for detecting clicks. Note that this is not the id of the button on the form.
	 *                                    Different dialogs can have the same button id.
	 *                                    To specify a control id for the button (for styling purposes for example), set the id in options.
	 * @param bool   $blnCausesValidation If the button causes the dialog to be validated before the action is executed
	 * @param bool   $blnIsPrimary        Whether this button will be automatically clicked if user presses an enter key.
	 * @param string $strConfirmation     If set, will confirm with the given string before the click is sent
	 * @param array  $attr             	  Additional attributes to add to the button tag. Will override all other attributes in the button.
	 * 									  Useful to do is button style, like ['class'=>'btn-primary']. Also useful is to automatically close
	 * 									  the dialog with ['data-dismiss'='modal']
	 */
	public function AddButton ($strButtonName,
							   $strButtonId = null,
							   $blnCausesValidation = false,
							   $blnIsPrimary = false,
							   $strConfirmation = null,
							   $attr = null) {
		if (!$this->mixButtons) {
			$this->mixButtons = [];
		}
		$btnOptions = [];
		if ($strConfirmation) {
			$btnOptions['confirm'] = $strConfirmation;
		}

		if (!$strButtonId) {
			$strButtonId = $strButtonName;
		}

		$btnOptions['id'] = $strButtonId;
		$btnOptions['text'] = $strButtonName;

		if ($attr) {
			$btnOptions['attr'] = $attr;
		}

		if ($blnIsPrimary) {
			$btnOptions['isPrimary'] = true;

			// Match the primary button style to the header style for a more pleasing effect. This can be overridden with the 'attr' option above.
			switch ($this->strDialogState) {
				case QDialog::StateError:
					$btnOptions['style'] = 'danger';
					break;

				case QDialog::StateHighlight:
					$btnOptions['style'] = 'warning';
					break;

				default:
					$btnOptions['style'] = 'primary';
					break;
			}
		}

		$this->mixButtons[] = $btnOptions;
		$this->blnValidationArray[$strButtonId] = $blnCausesValidation;
		$this->blnModified = true;
	}

	/**
	 * Remove the given button from the dialog.
	 *
	 * @param $strButtonId
	 */
	public function RemoveButton ($strButtonId) {
		if (!empty($this->mixButtons)) {
			$this->mixButtons = array_filter ($this->mixButtons, function ($a) use ($strButtonId) {return $a['id'] == $strButtonId;});
		}

		unset ($this->blnValidationArray[$strButtonId]);

		$this->blnModified = true;
	}

	/**
	 * Remove all the buttons from the dialog.
	 */
	public function RemoveAllButtons() {
		$this->mixButtons = array();
		$this->blnValidationArray = array();
		$this->blnModified = true;
	}

	/**
	 * Show or hide the given button. Changes the display attribute, so the buttons will reflow.
	 *
	 * @param $strButtonId
	 * @param $blnVisible
	 */
	public function ShowHideButton ($strButtonId, $blnVisible) {
		QApplication::ExecuteControlCommand($this->ControlId,  $this->GetJqSetupFunction(), 'showButton', $strButtonId, $blnVisible);
	}

	/**
	 * Applies CSS styles to a button that is already in the dialog.
	 *
	 * @param string $strButtonId Id of button to set the style on
	 * @param array $styles Array of key/value style specifications
	 */
	public function SetButtonStyle ($strButtonId, $styles) {
		QApplication::ExecuteControlCommand($this->ControlId,  $this->GetJqSetupFunction(), 'setButtonCss', $strButtonId, $styles);
	}

	/**
	 * Adds a close button that closes the dialog when clicked. Does not record the fact that it was clicked.
	 *
	 * @param $strButtonName
	 */
	public function AddCloseButton ($strButtonName) {
		$this->mixButtons[] = [
			'id' => $strButtonName,
			'text' => $strButtonName,
			'close' => true,
			'click' => false
		];
		$this->blnModified = true;
	}

	/**
	 * Create a message dialog. Automatically adds an OK button that closes the dialog. To detect the close,
	 * add an action on the Modal_HiddenEvent. To change the message, use the return value and set ->Text.
	 * To detect a button click, add a QDialog_ButtonEvent.
	 *
	 * If you specify no buttons, a close box in the corner will be created that will just close the dialog. If you
	 * specify just a string in $mixButtons, or just one string in the button array, one button will be shown that will just close the message.
	 *
	 * If you specify more than one button, the first button will be the default button (the one pressed if the user presses the return key). In
	 * this case, you will need to detect the button by adding a QDialog_ButtonEvent. You will also be responsible for calling "Close()" on
	 * the dialog after detecting a button.
	 *
	 * @param string $strMessage		// The message
	 * @param string|string[]|null $strButtons
	 * @param string|null $strControlId
	 * @return Modal
	 */
	public static function Alert($strMessage, $strButtons = null, $strControlId = null) {
		global $_FORM;

		$objForm = $_FORM;
		$dlg = new Modal($objForm, $strControlId);
		//$dlg->MarkAsModified(); // Make sure it gets drawn.
		$dlg->Text = $strMessage;
		$dlg->AddAction (new Modal_HiddenEvent(), new \QAjaxControlAction($dlg, 'Alert_Close'));
		if ($strButtons) {
			$dlg->blnHasCloseButton = false;
			if (is_string($strButtons)) {
				$dlg->AddCloseButton($strButtons);
			}
			elseif (count($strButtons) == 1) {
				$dlg->AddCloseButton($strButtons[0]);
			}
			else {
				$strButton = array_shift($strButtons);
				$dlg->AddButton($strButton, null, false, true);	// primary button

				foreach ($strButtons as $strButton) {
					$dlg->AddButton($strButton);
				}
			}
		} else {
			$dlg->blnHasCloseButton = true;
		}
		$dlg->ShowDialogBox();
		return $dlg;
	}

	/**
	 * An alert is closing, so we remove the dialog from the dom.
	 *
	 */
	public function Alert_Close() {
		$this->Form->RemoveControl($this->ControlId);
		QApplication::ExecuteControlCommand($this->getJqControlId(), 'remove');
	}

	/**
	 * Show the dialog. Implements the dialog interface.
	 **/
	public function ShowDialogBox() {
		$this->Visible = true; // will redraw the control if needed
		$this->Display = true; // will update the wrapper if needed
		$this->Open();
		//$this->blnWrapperModified = false;
	}

	/**
	 * Hide the dialog
	 */
	public function HideDialogBox() {
		$this->Close();
	}

	/**
	 * Show the dialog. Only works if dialog is already on the page in a hidden state.
	 */
	public function Open() {
		\QApplication::ExecuteControlCommand($this->ControlId, $this->GetJqSetupFunction(), 'open', QJsPriority::Low);
	}

	/**
	 * Hide the dialog
	 */
	public function Close() {
		\QApplication::ExecuteControlCommand($this->ControlId, $this->GetJqSetupFunction(), 'close', QJsPriority::Low);
	}

	/**
	 * Override to prevent validation state on the dialog itself.
	 */
	public function ReinforceValidationState()
	{
		// do nothing at the dialog level
	}

	/**
	 * Override to prevent the entire dialog from being redrawn while it is open. It can't do that. Mark
	 * individual items as modified instead.
	 */
	public function MarkAsWrapperModified()
	{
		if ($this->blnIsOpen) {
			// do nothing

		} else {
			parent::MarkAsWrapperModified();
		}
	}

	/**
	 * PHP magic method
	 *
	 * @param string $strName
	 * @param string $mixValue
	 *
	 * @throws \Exception|\QCallerException|\QInvalidCastException
	 */
	public function __set($strName, $mixValue) {
		switch ($strName) {
			case '_ClickedButton': // Internal only. Do not use. Used by JS above to keep track of clicked button.
				try {
					$this->strClickedButtonId = QType::Cast($mixValue, QType::String);
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;

			case '_IsOpen': // Internal only, to detect when dialog has been opened or closed.
				try {
					$this->blnIsOpen = QType::Cast($mixValue, QType::Boolean);

					// Setup wrapper style in case dialog is redrawn while it is open.
					if (!$this->blnIsOpen) {
						// dialog is closing, so reset all validation states.
						$this->Form->ResetValidationStates();
					}
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;

			// These options are specific to bootstrap's modal, but if there is a similar option in QDialog, we allow that also.

			case 'AutoOpen':	// the JQueryUI name of this option
			case 'Show':	// the Bootstrap name of this option
				try {
					$this->blnAutoOpen = QType::Cast($mixValue, QType::Boolean);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'HasCloseButton':
				try {
					$this->blnHasCloseButton = QType::Cast($mixValue, QType::Boolean);
					$this->blnCloseOnEscape = $this->blnHasCloseButton;
					$this->blnModified = true;	// redraw
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'CloseOnEscape' :	// JQuery UI version
			case 'Keyboard' :		// Bootstrap version
				try {
					$this->blnCloseOnEscape = QType::Cast($mixValue, QType::Boolean);
					$this->blnModified = true;	// redraw
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'Fade' :
				try {
					$this->blnFade = QType::Cast($mixValue, QType::Boolean);
					if ($this->blnFade) {
						$this->GetWrapperStyler()->AddCssClass('fade');
					} else {
						$this->GetWrapperStyler()->RemoveCssClass('fade');
					}
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'Title' :
				try {
					$this->strTitle = QType::Cast($mixValue, QType::String);
					$this->blnModified = true;	// redraw
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'Size' :
				try {
					$this->strSize = QType::Cast($mixValue, QType::String);
					$this->blnModified = true;	// redraw
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'Backdrop' :
				try {
					if ($mixValue === 'static') {
						$this->mixBackdrop = 'static';
					} else {
						$this->mixBackdrop = QType::Cast($mixValue, QType::Boolean);
					}
					$this->blnModified = true;	// redraw
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'HeaderClasses' :
				try {
					$this->strHeaderClasses = QType::Cast($mixValue, QType::String);
					$this->blnModified = true;	// redraw
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}


			// These options are part of the QDialog interface

			case 'DialogState':
				try {
					$this->strDialogState = QType::Cast($mixValue, QType::String);
					break;
				} catch (QInvalidCastException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			case 'Modal':
				// stub, does nothing
				break;

			default:
				try {
					parent::__set($strName, $mixValue);
					break;
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
			}
	}

	/**
	 * PHP magic method
	 *
	 * @param string $strName
	 *
	 * @return mixed
	 * @throws \Exception|\QCallerException
	 */
	public function __get($strName) {
		switch ($strName) {
			case 'ClickedButton': return $this->strClickedButtonId;
			case 'HasCloseButton' : return $this->blnHasCloseButton;
			case 'AutoOpen':
			case 'Show' : return $this->blnAutoOpen;
			case "CloseOnEscape": return $this->blnCloseOnEscape;
			case "HeaderClasses": return $this->strHeaderClasses;
			case "Backdrop": return $this->mixBackdrop;
			case "Fade": return $this->blnFade;
			case "Title": return $this->strTitle;
			case "Size": return $this->strSize;

			default:
				try {
					return parent::__get($strName);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}
}
?>