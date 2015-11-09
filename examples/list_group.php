<?php

require('../../../framework/qcubed.inc.php');
require('../includes/Control.trait.php');

use QCubed\Plugin\Bootstrap as Bs;

class SampleForm extends \QForm {
	protected $lg;
	protected $lblClicked;
	protected $pager;

	protected function Form_Create() {
		$this->lg = new Bs\ListGroup($this);
		$this->lg->SetDataBinder("lg_Bind");
		$this->lg->SetItemParamsCallback([$this, "lg_Params"]);
		$this->lg->AddClickAction(new QAjaxAction("lg_Action"));
		$this->lg->SaveState = true;

		$this->lblClicked = new QLabel($this);
		$this->lblClicked->Name = "Clicked on: ";

		$this->pager = new Bs\Pager($this);
		$this->pager->ItemsPerPage = 5;
		$this->lg->Paginator = $this->pager;

	}

	protected function lg_Bind() {
		$this->lg->TotalItemCount = Person::CountAll();
		$clauses[] = $this->lg->LimitClause;
		$this->lg->DataSource = Person::LoadAll($clauses);
	}

	public function lg_Params(Person $objPerson) {
		$a['id'] = 'lg_' . $objPerson->Id;
		$a['html'] = QApplication::HtmlEntities($objPerson->FirstName . ' ' . $objPerson->LastName);
		return $a;
	}

	protected function lg_Action($strFormId, $strControlId, $strActionParam) {
		$id = substr($strActionParam, 3);
		$objPerson = Person::Load($id);
		$this->lblClicked->Text = $objPerson->FirstName . ' ' . $objPerson->LastName;
	}


}

SampleForm::Run('SampleForm');
