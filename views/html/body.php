<!DOCTYPE html>
<html lang="fr">
	<head>	
		<?php echo $metas; ?>
		<title><?php echo $head_title ?></title>
		<?php echo $styles; ?>
		<?php echo $favicon; ?>
		
		<?php echo $scripts; ?>
		<?php if($config_javascript !== NULL): ?>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function() { 
				(new RootJS(<?php echo $config_javascript; ?>)).execute();
			}, false);
		</script>
		<?php endif; ?>
	</head>
	<body>
		<?php echo $content; ?>
	</body>
</html>