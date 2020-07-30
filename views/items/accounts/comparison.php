<?php use Root\Arr; ?>
<div class="account-statistic">
	<table>
		<tr class="head">
			<td>Mois</td>
			<?php foreach($years as $year): ?>
			<td><?php echo $year; ?></td>
			<td>Progression</td>
			<?php endforeach; ?>
		</tr>
		
		<?php foreach($months as $month): ?>
		<tr class="line">
			<td><?php echo $month; ?></td>
			<?php foreach($years as $year): ?>
			<td><?php echo Arr::get(Arr::get($data[$year], $month), 'amount'); ?></td>
			<td><?php echo Arr::get(Arr::get($data[$year], $month), 'progress'); ?></td>
			<?php endforeach; ?>
		</tr>
		<?php endforeach; ?>
		
		<tr class="line">
			<td>Total</td>
			<?php foreach($years as $year): ?>
			<td><?php echo Arr::get($data[$year], 'total'); ?></td>
			<td>-</td>
			<?php endforeach; ?>
		</tr>
		
	</table>
</div>