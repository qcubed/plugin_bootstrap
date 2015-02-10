<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/header.inc.php'); ?>
	<link href="<?= __BOOTSTRAP_CSS__ ?>" rel="stylesheet">

<?php $this->RenderBegin(); ?>


	<div class="instructions">
		<h1 class="instruction_title">QBootstrap: Form Examples</h1>

		<p>
			These examples show how to display form objects the Bootstrap way. It includes standard examples,
			but also some examples using some minor custom css modifications to allow more complex form layouts
			than the default Bootstrap provides.
		</p>
	</div>

	<ul class="nav nav-pills">
		<li role="presentation" class="active"><a href="<?= QApplication::$ScriptName . '?formName=forms1' ?>">Default</a></li>
		<li role="presentation"><a href="<?= QApplication::$ScriptName . '?formName=forms2' ?>">Inline</a></li>
		<li role="presentation"><a href="<?= QApplication::$ScriptName . '?formName=forms3' ?>">Horizontal</a></li>
		<li role="presentation"><a href="<?= QApplication::$ScriptName . '?formName=forms4' ?>">Horizontal 2</a></li>
		<li role="presentation"><a href="<?= QApplication::$ScriptName . '?formName=forms5' ?>">Horizontal 3</a></li>
	</ul>

	<h2>Default Bootstrap Form</h2>
	<div>
		<div class="row">
			<?php $this->firstName->RenderFormGroup(true, ['WrapperCssClass' => '+ col-sm-6']); ?>
			<?php $this->lastName->RenderFormGroup(true, ['WrapperCssClass' => '+ col-sm-6']); ?>
		</div>
		<?php $this->street->RenderFormGroup(); ?>
		<div class="row">
			<?php $this->city->RenderFormGroup(true, ['WrapperCssClass' => '+ col-sm-6']); ?>
			<?php $this->state->RenderFormGroup(true, ['WrapperCssClass' => '+ col-sm-2']); ?>
			<?php $this->zip->RenderFormGroup(true, ['WrapperCssClass' => '+ col-sm-4']); ?>

		</div>
		<?php $this->button->RenderFormGroup(); ?>

	</div>

<?php $this->RenderEnd(); ?>
<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/footer.inc.php'); ?>