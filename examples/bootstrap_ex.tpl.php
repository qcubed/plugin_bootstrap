<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/header.inc.php'); ?>
<link href="<?= __BOOTSTRAP_CSS__?>" rel="stylesheet">

<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<h1 class="instruction_title">QBootstrap: Support  and objects for Twitter Bootstrap</h1>

		<b>QBootstrap</b> is a collection of classes that integrate into QCubed to do two things:
		<ul>
			<li>Make all QCubed objects capable of being styled with bootstrap styles, and</li>
			<li>Provide specific <em>QControl</em> controls based on Bootstrap widgets.</li>
		</ul>
		<p>
			Be sure to read the ReadMe file for directions on how to install and set up the plugin.
			The setup process is a bit more complex than a standard plugin installation, but
			is still quite easy.
		</p>

	</div>

	<h2>
		Navbar
	</h2>
	<?php $this->navBar->Render(); ?>
	<h2>
		Carousel
	</h2>
	<?php $this->carousel->Render(); ?>
	<h2>
		Accordion
	</h2>
<?php $this->accordion->Render(); ?>
	<h2>
		Button Groups
	</h2>
<?php $this->lstRadio1->Render(); ?>
<?php $this->lstRadio2->Render(); ?>
	<h2>
		Dropdowns
	</h2>

<?php $this->lstPlain->Render(); ?>


<?php $this->RenderEnd(); ?>
<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/footer.inc.php'); ?>