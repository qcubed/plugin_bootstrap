<?php

	require_once('../../../framework/qcubed.inc.php');

	use QCubed\Plugin\Bootstrap as Bs;

	class SampleForm extends \QForm {
		protected $nav1;
		protected $nav2;

		protected function Form_Create() {
			$this->nav1 = new Bs\Nav($this);
			$objPanel = new \QPanel ($this->nav1);
			$objPanel->Name = 'Tab 1';
			$objPanel->Text = "This is the content of Tab 1";
			$objPanel = new \QPanel ($this->nav1);
			$objPanel->Name = 'Tab 2';
			$objPanel->Text = "And an example of content of Tab 2";
			$objPanel = new \QPanel ($this->nav1);
			$objPanel->Name = 'Tab 3';
			$objPanel->Text = "And an additional example of content of Tab 3";

			$this->nav2 = new Bs\Nav($this);
			$this->nav2->ButtonStyle = Bs\Bootstrap::NavPills;
			$objPanel = new \QPanel ($this->nav2);
			$objPanel->Name = 'Tab 3';
			$objPanel->Text = "This is the content of Tab 3";
			$objPanel = new \QPanel ($this->nav2);
			$objPanel->Name = 'Tab 4';
			$objPanel->Text = "And an example of content of Tab 4";

		}

	}

	SampleForm::Run('SampleForm');
