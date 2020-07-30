<?php if(count($items) > 1): ?>
<ul id="breadcrumb"><!--
	--><?php foreach($items as $key => $item): ?><!--
	--><li><?php echo $item; ?></li><!--
	--><?php endforeach; ?><!--
--></ul>
<?php endif; ?>