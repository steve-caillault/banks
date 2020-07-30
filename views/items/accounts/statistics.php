<div class="account-statistic">
	<h2><?php echo $title; ?></h2>
	
	<?php if(count($data['statistics']) == 0): ?>
	<p class="no-item">Il n'y a pas de données pour cette période.</p>
	<?php else: ?>
	<table>
		<tr class="head">
			<td>Date</td>
			<td>Montant</td>
		</tr>
		<?php foreach($data['statistics'] as $statistic): ?>
		<tr class="line">
			<td><?php echo $statistic['date']; ?></td>
			<td><?php echo $statistic['amount']; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endif; ?>
	
	<p>Montant : <?php echo $data['total']; ?></p>
	<p>Evolution : <?php echo $data['totalAverage']; ?></p>
</div>