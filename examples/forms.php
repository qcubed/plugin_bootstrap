<?php
require('../../../framework/qcubed.inc.php');
require('../includes/Control.trait.php');

	use QCubed\Plugin\Bootstrap as Bs;

	/**
	 * Normally you would set up your QControl class to inherit from the Bootstrap plugin's Control class. This is
	 * an alternate method of doing one-off Boostrap controls. We do that here because for this example, we might not
	 * have the ability to alter the QControl class.
	 */
	class MyTextBox extends Bs\TextBox {
		protected $strCssClass = Bs\Bootstrap::FormControl;
		use Bs\ControlTrait;
	}

	class MyButton extends Bs\Button {
		use Bs\ControlTrait;
	}


	class SampleForm extends QForm {

		protected $firstName;
		protected $lastName;
		protected $street;
		protected $city;
		protected $state;
		protected $zip;
		protected $button;

		protected function Form_Create() {
			$this->firstName = new MyTextBox ($this);	// Normally you would use Bs\Textbox here.
			$this->firstName->Name = QApplication::Translate('First Name');

			$this->lastName = new MyTextBox ($this);
			$this->lastName->Name = QApplication::Translate('Last Name');

			$this->street = new MyTextBox ($this);
			$this->street->Name = QApplication::Translate('Street');

			$this->city = new MyTextBox ($this);
			$this->city->Name = QApplication::Translate('City');

			$this->state = new MyTextBox ($this);
			$this->state->Name = QApplication::Translate('State');

			$this->zip = new MyTextBox ($this);
			$this->zip->Name = QApplication::Translate('Postal Code');

			$this->button = new MyButton ($this);	// Normally you would use Bs\Button here.
			$this->button->Text = 'OK';
		}

	}

	$formName = QApplication::QueryString('formName');
	if (!$formName) {
		$formName = 'forms1';
	}

	SampleForm::Run('SampleForm', $formName . '.tpl.php');
?>