<?php
/**
 * Implement a Bootstrap Nav object.
 *
 **/

namespace QCubed\Plugin\Bootstrap;

include_once("Bootstrap.php");

/**
 * Class Nav_SelectEvent
 * @package QCubed\Plugin\Bootstrap
 */
class Nav_SelectEvent extends \QEvent {
	const EventName = 'shown.bs.tab';
	const JsReturnParam = 'ui';
}


use \QType, \QApplication, \QHtml;

/**
 * Class Nav
 *
 * This Bootstrap Nav has many modes of operation, and can implement a Nav panel as described in the the Bootstrap
 * Components documentation, or a Tabs panel as described in the Bootstrap Javascript documentation.
 *
 * The simplest way to use it is to simply add QPanels to the control as child controls. Give each panel a Name, and
 * that will create a Tab panel.
 *
 * If you are just creating a Nav, or want more control of what is displayed, use the QHListControl functionality to
 * add items. The items will be drawn as Navs.
 *
 * @property string $ButtonStyle Either Bootstrap::NavPills or Bootstrap::NavTabs
 * @property string $Justified True to justify the items.
 *
 * @package QCubed\Plugin\Bootstrap
 */
class Nav extends \QHListControl {
	protected $strActiveItemId;
	//protected $blnUseAjax = true;

	/**
	 * @param \QControl|\QControlBase|\QForm $objParent
	 * @param null $strControlId
	 * @throws \Exception
	 * @throws \QCallerException
	 */
	public function __construct($objParent, $strControlId = null) {
		parent::__construct($objParent, $strControlId);

		$this->AddCssClass('nav nav-tabs');	// default to tabs
		$this->SetHtmlAttribute('role', 'tablist');

		$this->objItemStyle = new \QListItemStyle();
		$this->objItemStyle->SetHtmlAttribute('role', 'presentation');

		$this->AddAction(new Nav_SelectEvent(), new \QAjaxControlAction($this, 'tab_Click'));

		$this->blnUseWrapper = true;	// since its a compound control, a wrapper is required if redraw is forced.
		$this->blnIsBlockElement = true;
		Bootstrap::LoadJS($this);
	}

	/**
	 * @param \QHListItem $objItem
	 * @return \QListItemStyle
	 */
	protected function GetItemStyler ($objItem) {
		$objStyler = parent::GetItemStyler ($objItem);

		//if no item is active, pick the first item in the list to be active
		if ($this->strActiveItemId === null) {
			$this->strActiveItemId = $objItem->Id;
		}

		if ($objItem->Id === $this->strActiveItemId) {
			$objStyler->AddCssClass('active');
		}
		return $objStyler;
	}

	/**
	 * @param \QHListItem $objItem
	 * @return string
	 */
	protected function GetItemText ($objItem) {
		$strHtml = QApplication::HtmlEntities($objItem->Text);

		if ($strAnchor = $objItem->Anchor) {
			$attributes['href'] = '#' . $strAnchor;
			$attributes['aria-controls'] = $strAnchor;
		}
		$attributes['role'] = 'tab';
		$attributes['data-toggle'] = 'tab';

		$strHtml = QHtml::RenderTag('a',
				$attributes,
				$strHtml, false, true);
		return $strHtml;
	}

	/**
	 * A tab was clicked. Records the value of the clicked tab.
	 *
	 * @param $strFormId
	 * @param $strControlId
	 * @param $strParameter
	 */
	protected function tab_Click($strFormId, $strControlId, $strParameter) {
		$this->strActiveItemId = $strParameter;
	}

	/**
	 * Returns the HTML for the control and all subitems. If no items or panels are added, nothing will be drawn.
	 *
	 * @return string
	 */
	public function GetControlHtml() {
		$strHtml = '';
		if ($this->HasDataBinder()) {
			$this->CallDataBinder();
		}
		$strHtml = $this->RenderNav();
		$strHtml .= $this->RenderPanels();

		if ($this->HasDataBinder()) {
			$this->RemoveAllItems();
		}

		return $strHtml;
	}

	/**
	 * Renders the nav tag.
	 *
	 * @return string
	 */
	protected function RenderNav() {
		$strHtml = '';

		// If items are present, use them
		if ($this->GetItemCount()) {
			foreach ($this->GetAllItems() as $objItem) {
				$strHtml .= $this->GetItemHtml($objItem);
			}
		} else {	// Otherwise, use the names of child panels
			foreach ($this->GetChildControls() as $objControl) {
				$strHtml .= $this->RenderChildPanelName($objControl);
			}
		}
		if ($strHtml) {
			$strHtml = $this->RenderTag($this->strTag, null, null, $strHtml);
		}
		return $strHtml;
	}

	/**
	 * Renders the child panels as follow on panes.
	 *
	 * @param \QControl $objControl
	 * @return string
	 */
	protected function RenderChildPanelName(\QControl $objControl) {
		if ($this->strActiveItemId === null) {
			$this->strActiveItemId = $objControl->ControlId;
		}

		$strAnchor = QHtml::RenderTag('a', ['href'=>'#' . $objControl->ControlId . "_tab", 'aria-controls'=>$objControl->ControlId, 'role'=>'tab', 'data-toggle'=>'tab'], $objControl->Name, false, true);
		$attributes['role']='presentation';
		if ($objControl->ControlId === $this->strActiveItemId) {
			$attributes['class'] = 'active';
		}
		$strHtml = QHtml::RenderTag('li', $attributes, $strAnchor);
		return $strHtml;
	}

	/**
	 * @return string
	 * @throws \Exception
	 * @throws \QCallerException
	 */
	protected function RenderPanels() {
		$strHtml = '';
		foreach ($this->GetChildControls() as $objControl) {
			// Render each child control inside a tab pane, so that child control can have a wrapper if desired.
			$attr['role'] = 'tabpanel';
			$attr['class'] = 'tab-pane';
			if ($objControl->ControlId === $this->strActiveItemId) {
				$attr['class'] .= ' active';
			}
			$attr['id'] = $objControl->ControlId . "_tab";
			$strHtml .= QHtml::RenderTag('div', $attr, $objControl->Render(false));
		}

		$attributes['class'] = 'tab-content';
		if ($this->HasCssClass(Bootstrap::NavTabs)) {
			$attributes['class'] .= ' qbstabs-content';
		} else {
			$attributes['class'] .= ' qbspills-content';
		}
		if (!empty($strHtml)) {
			$strHtml = QHtml::RenderTag('div', $attributes, $strHtml);
		}
		return $strHtml;
	}

	/**
	 * @param string $strName
	 * @return mixed
	 * @throws QCallerException
	 * @throws \Exception
	 * @throws \QCallerException
	 */
	public function __get($strName) {
		switch ($strName) {
			case 'ButtonStyle': return $this->buttonStyle;
			case 'Justified': return $this->blnJustified;
			default:
				try {
					return parent::__get($strName);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}

	/**
	 * @param string $strName
	 * @param string $mixValue
	 * @throws QCallerException
	 * @throws \Exception
	 * @throws \QCallerException
	 * @throws \QInvalidCastException
	 */
	public function __set($strName, $mixValue) {
		switch ($strName) {
			case 'ButtonStyle':
				try {
					$buttonStyle = QType::Cast($mixValue, QType::String);
					$this->RemoveCssClass(Bootstrap::NavPills);
					$this->RemoveCssClass(Bootstrap::NavTabs);
					$this->AddCssClass($buttonStyle);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;

			case 'Justified':
				try {
					$blnJustified = QType::Cast($mixValue, QType::Boolean);
					if ($blnJustified) {
						$this->AddCssClass(Bootstrap::NavJustified);
					} else {
						$this->RemoveCssClass(Bootstrap::NavJustified);
					}
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;

			default:
				try {
					parent::__set($strName, $mixValue);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
				break;
		}
	}

}