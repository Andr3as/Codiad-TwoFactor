<?php
	require_once('class.tfa.php');
	$Auth = new TFA();
	
	$Auth->username = $_SESSION['user'];
?>
<div class="codegit_settings">
	<label><span class="icon-key big-icon"></span>TwoFactor</label>
	<hr>
	<div style="margin-left: 20px;">
		<?php
			echo '<h4 style="font-size: 15px; font-weight: bold;">';
			if ($Auth->IsTFAEnabled()) {
				i18n("Disable Two Factor Authentication");
			} else {
				i18n("Enable Two Factor Authentication");
			}
			echo "</h4><br>";
			i18n("Enter your password and token as confirmation");
		?>
	
		<div id="tfa_form" style="margin-top: 20px;">
			<div style="width: 50%; float: left;">
				<label><span class="icon-lock login-icon"></span> <?php i18n("Password"); ?></label>
				<input type="password" class="password" style="width: 200px">
				<label><span class="icon-lock login-icon"></span> <?php i18n("Token"); ?></label>
				<input type="token" class="token" style="width: 200px">
				<button class="button">
				<?php
					if ($Auth->IsTFAEnabled()) {
						i18n("Disable");
					} else {
						i18n("Enable");
					}
				?>
				</button>
			</div>
			<div style="width: 50%; float: left;">
				<?php
					if (!$Auth->IsTFAEnabled()) {
						//QR Code
						?>
						<img src="<?php echo $Auth->GenerateSecret(); ?>">
						<?php
					}
				?>
			</div>
		</div>
		<?php
		?>
	</div>
</div>