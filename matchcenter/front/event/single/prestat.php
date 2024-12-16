<?php
/**
 * Template part for event statistics predict block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$data = $event->getPreStatistics();
?>

<h1>Статистика и прогноз</h1>

<div class="sp_block">
	<h2 class="stat_row_name">Прогнозы от API</h2>

	<div class="stat_results">
		<?php foreach ( $data['prediction'] as $pred ) : ?>
			<span style="color:<?php echo $pred['color']; ?>"><?php echo $pred['text'] ?></span>
		<?php endforeach; ?>
	</div>
</div>

<div class="sp_block">
	<h2 class="stat_row_name">Сравнительная статистика</h2>
	<?php foreach ( $data['compare'] as $key => $compare ) : ?>
		<div class="stat_compare_name"></div>
		<div class="stat_compare_wrapper">
			<div class="stat_compare_line stat_compare_line_left">
				<div class="stat_compare_home" style="width:<?php echo $compare['home']; ?>"></div>
			</div>
			<div class="stat_compare_values">
				<div><?php echo $compare['home']; ?></div>
				<div><?php echo $key ?></div>
				<div><?php echo $compare['away']; ?></div>
			</div>
			<div class="stat_compare_line stat_compare_line_right">
				<div class="stat_compare_away" style="width:<?php echo $compare['away']; ?>"></div>
			</div>
		</div>
	<?php endforeach; ?>	
</div>

<div class="sp_block">
	<h2 class="stat_row_name">Последние 5 игр</h2>
	<div class="stat_rows">
		<?php foreach ( $data['last_5'] as $key => $value ) : ?>
			<div class="stat_row">
				<div class="stat_row_value stat_row_value_home">
					<span><?php echo $value['home']; ?></span>
				</div>
				<div class="stat_row_name"><?php echo $key; ?></div>
				<div class="stat_row_value stat_row_value_away">
					<span><?php echo $value['away']; ?></span>
				</div>			
			</div>
		<?php endforeach; ?>
			
	</div>
</div>


<div class="sp_block">
	<h2 class="stat_row_name">Игры в чемпионате</h2>
	<div class="stat_rows">
		<div class="stat_row">
			<div class="stat_row_value stat_row_value_home">
				<?php foreach ( $data['league']['form']['home'] as $form ) : ?>
					<div class="stat_form stat_form_<?php echo $form; ?>"></div>
				<?php endforeach; ?>
			</div>
			<div class="stat_row_name">Форма</div>
			<div class="stat_row_value stat_row_value_away">
				<?php foreach ( $data['league']['form']['away'] as $form ) : ?>
					<div class="stat_form stat_form_<?php echo $form; ?>"></div>
				<?php endforeach; ?>
			</div>			
		</div>
		<div class="stat_row">
			<div class="stat_row_value stat_row_value_home">
				<span>Дома</span>
				<span>Все</span>
			</div>
			<div class="stat_row_name"></div>
			<div class="stat_row_value stat_row_value_away">
				<span>Все</span>
				<span>В гостях</span>
			</div>	
		</div>
		<?php foreach ( $data['league']['total'] as $key => $value ) : ?>
			<div class="stat_row">
				<div class="stat_row_value stat_row_value_home">
					<span><?php echo $data['league']['home'][$key]['home']; ?></span>
					<span><?php echo $value['home']; ?></span>
				</div>
				<div class="stat_row_name"><?php echo $key; ?></div>
				<div class="stat_row_value stat_row_value_away">
					<span><?php echo $value['away']; ?></span>
					<span><?php echo $data['league']['away'][$key]['away']; ?></span>
				</div>			
			</div>
		<?php endforeach; ?>
			
	</div>
</div>

<div class="sp_block">
	<h2 class="stat_row_name">Head to head</h2>
	<div class="stat_rows">
		<div class="stat_row">
			<div class="stat_row_value stat_row_value_home">
				<span>Дома</span>
				<span>Все</span>
			</div>
			<div class="stat_row_name"></div>
			<div class="stat_row_value stat_row_value_away">
				<span>Все</span>
				<span>В гостях</span>
			</div>	
		</div>
		<?php foreach ( $data['games']['total'] as $key => $value ) : ?>
			<div class="stat_row">
				<div class="stat_row_value stat_row_value_home">
					<span><?php echo $data['games']['home'][$key]['home']; ?></span>
					<span><?php echo $value['home']; ?></span>
				</div>
				<div class="stat_row_name"><?php echo $key; ?></div>
				<div class="stat_row_value stat_row_value_away">
					<span><?php echo $value['away']; ?></span>
					<span><?php echo $data['games']['away'][$key]['away']; ?></span>
				</div>			
			</div>
		<?php endforeach; ?>

	</div>
</div>
<div class="sp_block">
	<div class="sp-table-wrapper">
		<table>
			<thead>
				<tr>
					<th>Дата</th>
					<th>Матч</th>
					<th>Счет</th>
					<th>Лига</th>
					<th>Судья</th>
				</tr>
			</thead>
			<?php foreach ( $data['games']['games'] as $game ) : ?>
				<tr>
					<td><?php echo $game['date'] ?></td>
					<td><?php echo $game['name'] ?></td>
					<td><?php echo $game['score'] ?></td>
					<td><?php echo $game['day'] ?></td>
					<td><?php echo $game['referee'] ?></td>	
				</tr>
			<?php endforeach; ?>
			
		</table>
	</div>

</div>

<?php
echo '<pre>';
//print_r($data);
echo '</pre>';
?>