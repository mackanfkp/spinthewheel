<div class="container">
	<div id="wallet-bar">
		<ul class="inline pull-left">
			<li>
				Balance: 
				<span class="balance">
					&euro;<?php echo $realmoney->get('current_value') + ($bonus ? $bonus->get('current_value') : 0); ?>
				</span>
			</li>
			<li class="">|</li>
			<li>
				Real money wallet:
				<span class="rm_current_value">
					&euro;<?php echo $realmoney->get('current_value'); ?>
				</span>
			</li>
		</ul>

		<ul class="inline bonus pull-left">
			<?php if ($bonus): ?>
				<li class="">|</li>
				<li class="type">
					Bonus wallet:
					<span class="bm_current_value">
						&euro;<?php echo $bonus->get('current_value'); ?>
					</span>
				</li>
				<li class="">|</li>
				<li>
					Bet to clear:
					<span class="bm_wagered_value">
						&euro;<?php echo $bonus->get('wagered_value'); ?>
					</span>
				</li>
				<li class="">|</li>
				<li class="forfeit"><?php echo anchor('game/forfeit', 'Forfeit bonus', 'onclick="return confirm(\'Are you sure\');"'); ?>
			<?php endif; ?>
		</ul>
		<div class="clearfix"></div>
	</div>

	<br>

	<h2>Spin the Wheel</h2>

	<div class="pull-left">
		<?php echo form_open('game/spin',  array('class' => 'form')) ?>
			<div class="control-group">
				<div class="controls pull-left" style="margin-right: 15px;">
					<label for="bet">Bet amount:</label>
					<select id="bet" class="input-medium">
						<option value="1">&euro; 1</option>
						<option value="5">&euro; 5</option>
						<option value="10">&euro; 10</option>
					</select>
				</div>
	
				<div class="controls pull-left">
					<label>&nbsp;</label>
					<button id="spin" class="btn">Spin</button>
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
	</div>

	<div class="pull-right" style="width: 70%;">
		<div class="alert alert-info" id="result" style="height: 200px; overflow: auto;">
			<h4>History</h4>
			<p></p>
		</div>

		<a href="javascript:;" class="clear">&raquo; Clear history</a>
	</div>

	<div class="clearfix"></div>
</div>