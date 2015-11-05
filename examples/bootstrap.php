<?php
	require_once('../../../framework/qcubed.inc.php');

	use QCubed\Plugin\Bootstrap as Bs;

	class SampleForm extends QForm {
		protected $navBar;
		protected $carousel;

		protected function Form_Create() {
			$this->NavBar_Create();
			$this->Carousel_Create();
		}

		protected function NavBar_Create() {
			$this->navBar = new Bs\Navbar($this, 'navbar');

			//$this->objMenu->AddCssClass('navbar-ryaa');
			$url = __PHP_ASSETS__ . '/_devtools/start_page.php';
			$this->navBar->HeaderText = sprintf ('<img class="logo" src="%s/qcubed_logo_footer.png" alt="Logo" >', __IMAGE_ASSETS__);
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
	}

	SampleForm::Run('SampleForm');
?>