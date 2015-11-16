<?php

namespace QCubed\Plugin\Bootstrap;

include_once("Bootstrap.php");

/**
 * Class ListGroup
 * Implements a list group as a data repeater.
 * If you set SaveState = true, it will remember what was clicked and make it active.
 * Uses a proxy to display the items and process clicks.
 *
 * @property string $SelectedId
 *
 * @package QCubed\Plugin\Bootstrap
 */
class ListGroup extends \QDataRepeater {

	/** @var \QControlProxy */
	protected $prxButton = null;

	protected $strSelectedItemId = null;

	protected $itemParamsCallback = null;

	/**
	 * @param \QControl|\QControlBase|\QForm $objParentObject
	 * @param null $strControlId
	 * @throws \Exception
	 * @throws \QCallerException
	 */
	public function __construct($objParentObject, $strControlId = null)
	{
		try {
			parent::__construct($objParentObject, $strControlId);
		} catch (\QCallerException  $objExc) {
			$objExc->IncrementOffset();
			throw $objExc;
		}

		$this->prxButton = new \QControlProxy($this);
		$this->Setup();
		$this->AddCssClass("list-group");
	}


	/**
	 * @throws \QCallerException
	 */
	protected function Setup() {
		// Setup Pagination Events
		$this->prxButton->AddAction(new \QClickEvent(), new \QAjaxControlAction($this, 'Item_Click'));
	}

	/**
	 * Given an action, will make it a click action of an item. The item's id will be in the returned parameter.
	 * @param \QAction $action
	 * @throws \QCallerException
	 */
	public function AddClickAction(\QAction $action) {
		$this->prxButton->AddAction(new \QClickEvent(), $action);
	}

	/**
	 * Uses the temaplate and/or HTML callback to get the inner html of each link.  Relies on the ItemParamsCallback
	 * to return information on how to draw each item.
	 *
	 * @param $objItem
	 * @return string
	 * @throws QCallerException
	 */
	protected function GetItemHtml($objItem) {
		if (!$this->itemParamsCallback) {
			throw new \Exception("Must provide an itemParamsCallback");
		}

		$params = call_user_func($this->itemParamsCallback, $objItem, $this->intCurrentItemIndex);
		$strLabel = "";
		if (isset($params["html"])) {
			$strLabel = $params["html"];
		}
		$strId = "";
		if (isset($params["id"])) {
			$strId = $params["id"];
		}
		$strActionParam = $strId;
		if (isset($params["action"])) {
			$strActionParam = $params["action"];
		}

		$attributes = [];
		if (isset($params["attributes"])) {
			$attributes = $params["attributes"];
		}

		if (isset($attributes["class"])) {
			$attributes["class"] .= " list-group-item";
		} else {
			$attributes["class"] = "list-group-item";
		}

		if ($this->blnSaveState && $this->strSelectedItemId !== null && $this->strSelectedItemId == $strId) {
			$attributes["class"] .= " active";
		}
		$strLink = $this->prxButton->RenderAsLink($strLabel, $strActionParam, $attributes, "a", false);

		return $strLink;
	}

	/**
	 * An item in the list was clicked. This records what item was last clicked.
	 *
	 * @param $strFormId
	 * @param $strControlId
	 * @param $strParam
	 */
	public function Item_Click($strFormId, $strControlId, $strParam) {
		if ($strParam) {
			$this->strSelectedItemId = $strParam;
			if ($this->blnSaveState) {
				$this->blnModified = true;
			}
		}
	}

	/**
	 * @return mixed
	 */
	protected function GetState() {
		$state = parent::GetState();
		if ($this->strSelectedItemId !== null) {
			$state["sel"] = $this->strSelectedItemId;
		}
		return $state;
	}

	/**
	 * @param mixed $state
	 */
	protected function PutState($state) {
		if (isset ($state["sel"])) {
			$this->strSelectedItemId = $state["sel"];
		}
		parent::PutState($state);
	}

	/**
	 * Set the item params callback. The callback should be of the form:
	 *  func($objItem, $intCurrentItemIndex)
	 * The callback will be give the current item from the data source, and the item's index visually.
	 * The function should return a key/value array with the following possible items:
	 *	html - the html to display as the innerHtml of the row.
	 *  id - the id for the row tag
	 *  attributes - Other attributes to put in the row tag.
	 *
	 * The callback is a callable, so can be of the form [$objControl, "func"]
	 *
	 * The row will automatically be given a class of "list-group-item", and the active row will also get the "active" class.
	 *
	 * @param callable $callback
	 */
	public function SetItemParamsCallback(callable $callback) {
		$this->itemParamsCallback = $callback;
	}

	public function Sleep() {
		$this->itemParamsCallback = \QControl::SleepHelper($this->itemParamsCallback);
		parent::Sleep();
	}

	/**
	 * The object has been unserialized, so fix up pointers to embedded objects.
	 * @param QForm $objForm
	 */
	public function Wakeup(\QForm $objForm) {
		parent::Wakeup($objForm);
		$this->itemParamsCallback = \QControl::WakeupHelper($objForm, $this->itemParamsCallback);
	}

	/**
	 * @param string $strName
	 * @param string $mixValue
	 * @throws QInvalidCastException
	 * @throws \Exception
	 * @throws \QCallerException
	 * @throws \QInvalidCastException
	 */
	public function __set($strName, $mixValue) {
		switch ($strName) {
			case 'SelectedId':
				$this->blnModified = true;
				$this->strSelectedItemId = $mixValue; // could be string or integer
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

	/**
	 * @param string $strName
	 * @return int|mixed|null|string
	 * @throws QCallerException
	 * @throws \Exception
	 * @throws \QCallerException
	 */
	public function __get($strName) {
		switch ($strName) {
			case 'SelectedId':
				return $this->strSelectedItemId;

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