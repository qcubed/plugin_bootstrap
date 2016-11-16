<?php

	require_once('../../../qcubed/qcubed.inc.php');

	use QCubed\Plugin\Bootstrap as Bs;

	class SampleForm extends \QForm {
		/** @var  Bs\Modal */
		protected $modal1;
		/** @var Bs\ Button */
		protected $btn1;

		protected $modal2;
		protected $btn2;

		protected function Form_Create() {
			$this->btn1 = new Bs\Button($this);
			$this->btn1->AddAction(new \QClickEvent(), new \QAjaxAction('ShowDialog'));
			$this->btn1->ActionParameter = 1;
			$this->btn1->Text = "Show Modal 1";

			$this->modal1 = new Bs\Modal($this);
			$this->modal1->Text = "Hi there";
			$this->modal1->Title = "Simple Modal";

			$this->btn2 = new Bs\Button($this);
			$this->btn2->AddAction(new \QClickEvent(), new \QAjaxAction('ShowDialog'));
			$this->btn2->ActionParameter = 2;
			$this->btn2->Text = "Show Modal 2";

			$this->modal2 = new Bs\Modal($this);
			$this->modal2->Text = "Hi there";
			$this->modal2->Title = "Modal with Buttons";
			$this->modal2->AddButton('Watch Out', 'wo', false, false, "Are you sure?", ['class'=>Bs\Bootstrap::ButtonWarning]);
			$this->modal2->AddCloseButton('Cancel');
			$this->modal2->AddButton('OK', 'ok', false, true);
			$this->modal2->AddAction (new QDialog_ButtonEvent(), new QAjaxAction('ButtonClick2'));
		}

		public function ShowDialog($strFormId, $strControlId, $strActionParam) {
			$strControlName = 'modal' . $strActionParam;
			$this->$strControlName->ShowDialogBox();
		}

		public function ButtonClick2($strFormId, $strControlId, $strParameter) {
			$this->modal2->HideDialogBox();
			Bs\Modal::Alert("Button '" . $strParameter . "' was clicked");
		}
	}

	SampleForm::Run('SampleForm');
