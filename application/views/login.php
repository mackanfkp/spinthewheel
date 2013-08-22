<div class="container">
	<h1>Login</h1>

	<?php echo form_open('main/login',  array('class' => 'form')) ?>

		<div class="errors">
			<?php echo validation_errors(); ?>
		</div>

		<div class="control-group">
			<?php echo form_label('Email', 'username'); ?>

			<div class="controls">
				<?php echo form_input($inputs['username']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Password', 'password'); ?>

			<div class="controls">
				<?php echo form_password($inputs['password']); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn-primary" name="login" value="Login" />
			</div>
		</div>
	</form>
</div>