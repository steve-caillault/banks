<header>
	<?php echo $homeAnchor; ?>
	<?php if($mainMenu = $menus['main']): ?>
	<div class="menu menu-main"><?php echo $mainMenu; ?></div>
	<?php endif; ?>
	<?php if($userMenu = $menus['user']): ?>
	<div tabindex="0" class="menu menu-user">
		<label for="user-menu-button">Menus</label>
		<div class="content">
			<?php echo $userMenu; ?>
		</div>
	</div>
	<?php endif; ?>
</header>