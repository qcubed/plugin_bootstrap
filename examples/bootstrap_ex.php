<?php
	require_once('../../../framework/qcubed.inc.php');

	use QCubed\Plugin\Bootstrap as Bs;

	class SampleForm extends QForm {
		protected $navBar;
		protected $carousel;
		/** @var  Bs\Accordion */
		protected $accordion;

		protected $lstRadio1;
		protected $lstRadio2;

		protected $lstPlain;

		protected function Form_Create() {
			$this->NavBar_Create();
			$this->Carousel_Create();
			$this->Accordion_Create();
			$this->RadioList_Create();
			$this->Dropdowns_Create();
		}

		protected function NavBar_Create() {
			$this->navBar = new Bs\Navbar($this, 'navbar');

			//$this->objMenu->AddCssClass('navbar-ryaa');
			$url = __PHP_ASSETS__ . '/_devtools/start_page.php';
			$this->navBar->HeaderText = QHtml::RenderTag("img",
				["class"=>"logo", "src"=>__IMAGE_ASSETS__ . "/qcubed_logo_footer.png", "alt"=>"Logo"], null, true);
			$this->navBar->HeaderAnchor = $url;
			$this->navBar->StyleClass = Bs\Bootstrap::NavbarInverse;

			$objList = new Bs\NavbarList($this->navBar);
			$objListMenu = new Bs\NavbarDropdown('List');
			$objEditMenu = new Bs\NavbarDropdown('New');

			// Add all the lists and edits in the drafts directory
			$list = scandir (__DOCROOT__ . __FORMS__);
			foreach ($list as $name) {
				if ($offset = strpos ($name, '_list.php')) {
					$objListMenu->AddItem (new Bs\NavbarItem(substr ($name, 0, $offset), null, __FORMS__ . '/' .  $name));
				}
				elseif ($offset = strpos ($name, '_edit.php')) {
					$objEditMenu->AddItem (new Bs\NavbarItem(substr ($name, 0, $offset), null, __FORMS__ . '/' . $name));
				}
			}

			$objList->AddMenuItem($objListMenu);;
			$objList->AddMenuItem($objEditMenu);

			/*

			$objRandomMenu = new Bs\NavbarDropdown('Contribute');

			$objList->AddMenuItem (new Bs\NavbarItem("Login", __SUBDIRECTORY__ . '/private/login.html', 'navbarLogin'));
			*/

		}

		protected function Carousel_Create() {
			$this->carousel = new Bs\Carousel ($this);
			$this->carousel->AddListItem(new Bs\CarouselItem('cat.jpg', 'Cat'));
			$this->carousel->AddListItem(new Bs\CarouselItem('rhino.jpg', 'Rhino'));
			$this->carousel->AddListItem(new Bs\CarouselItem('pig.jpg', 'Pig'));
		}

		protected function Accordion_Create() {
			$this->accordion = new Bs\Accordion($this);
			$this->accordion->SetDataBinder("Accordion_Bind");
			$this->accordion->SetDrawingCallback([$this, "Accordion_Draw"]);
		}

		protected function Accordion_Bind() {
			$this->accordion->DataSource = Person::LoadAll([QQ::Expand(QQN::Person()->Address)]);
		}

		public function Accordion_Draw($objAccordion, $strPart, $objItem, $intIndex) {
			switch ($strPart) {
				case Bs\Accordion::RenderHeader:
					$objAccordion->RenderToggleHelper(QApplication::HtmlEntities($objItem->FirstName . ' ' . $objItem->LastName));
					break;

				case Bs\Accordion::RenderBody:
					if ($objItem->Address) {
						echo "<b>Address: </b>" . $objItem->Address->Street . ", " . $objItem->Address->City . "<br />";
					}
					break;
			}
		}

		protected function RadioList_Create() {
			$this->lstRadio1 = new Bs\RadioList($this);
			$this->lstRadio1->AddItems(["yes"=>"Yes", "no"=>"No"]);

			$this->lstRadio2 = new Bs\RadioList($this);
			$this->lstRadio2->AddItems(["yes"=>"Yes", "no"=>"No"]);
			$this->lstRadio2->ButtonMode = QRadioButtonList::ButtonModeSet;
			$this->lstRadio2->ButtonStyle = Bs\Bootstrap::ButtonPrimary;

		}

		protected function Dropdowns_Create() {
			$selItems = [
				new Bs\DropdownItem("First"),
				new Bs\DropdownItem("Second"),
				new Bs\DropdownItem("Third")

			];
			$this->lstPlain = new Bs\Dropdown($this);
			$this->lstPlain->Text = "Plain";
			$this->lstPlain->AddItems($selItems);
		}


	}

	SampleForm::Run('SampleForm');
?>