<ul class="pagination">
	<?php if ($current == 1): // affichage de la première page ?>
	<li class="page"><span>1</span></li>
	<?php else: // lien vers la première page ?>
	<li class="page">
		<a href="<?php echo $pages[1]; ?>" title="" rel="first">1</a>
	</li>
	<?php endif;
	if ($total >= 3): // affichage de la deuxième page
	if ($current == 2): ?>
	<li class="page"><span>2</span></li>
		<?php else: ?>
	<li class="page"><a href="<?php echo $pages[2]; ?>">2</a></li>
		<?php endif;
	endif; 
	if ($total > 4 AND $total <= 11): // le nombre de page est compris entre 5 et 10 pages
		for ($i = 3 ; $i < $total - 1 ; $i++):
		if ($current == $i): ?>
	<li class="page"><span><?php echo $current ?></span></li>
			<?php else: ?>
	<li class="page"><a href="<?php echo $pages[$i]; ?>"><?php echo $i ?></a></li>
			<?php endif;
		endfor; ?>
	<?php endif;
	if ($total > 11): // le nombre de page est supérieur à 11
	if ($current < 3): 
			for ($i = 3 ; $i <= 7 ; $i++):
			if ($i == $current): ?>
	<li class="page"><span><?php echo $current; ?></span></li>
				<?php else: ?>
	<li class="page"><a href="<?php echo $pages[$i]; ?>"><?php echo $i ?></a></li>
				<?php endif;
			endfor; ?>
	<li class="page">...</li>
		<?php else:
		if ($current > 6): ?>
	<li class="page">...</li>
				<?php endif;
				for ($i = $current - 3 ; $i < $current ; $i++):
					if ($i > 2): ?>
	<li class="page"><a href="<?php echo $pages[$i]; ?>"><?php echo $i ?></a></li>
					<?php endif;
				endfor;
				if ($current < $total - 1): ?>
	<li class="page"><span><?php echo $current ?></span></li>
				<?php endif;
				for ($i = $current + 1 ; $i < $current + 4 ; $i++):
					if ($i < $total - 1): ?>
	<li class="page"><a href="<?php echo $pages[$i]; ?>"><?php echo $i ?></a></li>
					<?php endif;
				endfor;
				if ($current < $total - 5): ?>
	<li class="page">...</li>
				<?php endif;
			endif;
		endif;
	if ($total - 1 >= 3): // affichage de l'avant dernière page
	if ($current == $total - 1): ?>
	<li class="page"><span><?php echo $current ?></span></li>
		<?php elseif ($current < $total OR ($total == $current AND $total <= 11)): ?>
	<li class="page"><a href="<?php echo $pages[$total - 1];  ?>"><?php echo $total - 1 ?></a></li>
		<?php endif;
	endif;
	if ($current != $total): // afficahge de la dernière page ?>
	<li class="page"><a href="<?php echo $pages[$total]; ?>"><?php echo $total; ?></a></li>
	<?php else: ?>
	<li class="page"><span><?php echo $total; ?></span></li>
	<?php endif ?>
</ul>