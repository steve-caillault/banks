<?php foreach($rules as $rule): ?>
User-agent: <?php echo $rule['user_agent']."\n"; ?>
<?php echo $rule['action']; ?>: <?php echo $rule['path']."\n"; ?>
<?php endforeach; ?>
Sitemap: <?php echo $sitemap_url; ?>