<?php
/**
 * Template for main page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();

$winline = (new SP_SNS_API_WINLINE());
$fonbet = (new SP_SNS_API_FONBET());
$betboom = (new SP_SNS_API_BETBOOM());

?>

<?php if ( have_posts() ) {
	while ( have_posts() ) : the_post(); ?>
<div class="wrapper">
	<?php

		$winline_teams = $winline->getTeams();
		$fonbet_teams = $fonbet->getTeams();
		$betboom_teams = $betboom->getTeams();
		//print_r($fonbet->getTeams());

		$winline_sports = $winline->getSports();

		$request = 'tournaments?season=2024';
		$tennis_feeds = SP_Loader_Functions::getFeeds($request, 'tennis');
 

	
	?>

	<div class="sp_block_accord">
		<h3 class="sp_block_accord_title sp_event_rows_title">БК ID Клубы</h3>
		<div class="sp_block_accord_content">
			<div class="sp-table-wrapper">
				<table>
					<thead>
						<tr>
							<th>Букмекер</th>
							<th>ID</th>
							<th>Клуб</th>
							<th>Лига</th>
						</tr>
					</thead>
					<?php foreach ( $winline_teams as $team ) : ?>
						<tr>
							<td>Винлайн</td>
							<td><?php echo $team['id'] ?></td>
							<td><?php echo $team['name'] ?></td>
							<td><?php echo $team['league'] ?></td>
							
						
							
						</tr>
					<?php endforeach; ?>
					<?php foreach ( $fonbet_teams as $team ) : ?>
						<tr>
							<td>Фонбет</td>
							<td><?php echo $team['id'] ?></td>
							<td><?php echo $team['name'] ?></td>
							<td><?php echo $team['league'] ?></td>
							
							
							
						</tr>
					<?php endforeach; ?>
					<?php foreach ( $betboom_teams as $team ) : ?>
						<tr>
							<td>Бетбум</td>
							<td><?php echo $team['id'] ?></td>
							<td><?php echo $team['name'] ?></td>
							<td><?php echo $team['league'] ?></td>
							
							
							
						</tr>
					<?php endforeach; ?>				
				</table>
			</div>
		</div>
	</div>

	<div class="sp_block_accord">
		<h3 class="sp_block_accord_title sp_event_rows_title">БК ID турниры</h3>
		<div class="sp_block_accord_content">
			<div class="sp-table-wrapper">
				<table>
					<thead>
						<tr>
							<th>Букмекер</th>
							<th>ID</th>
							<th>Вид спорта</th>
							<th>Турнир</th>
							<th>Страна</th>
						</tr>
					</thead>					
					<?php foreach ( $winline_sports as $key => $sports ) : ?>
						<?php foreach ( $sports as $sport ) : ?>
							<tr>
								<td>Винлайн</td>
								<td><?php echo $sport[2] ?></td>
								<td><?php echo $key ?></td>
								<td><?php echo $sport[1] ?></td>
								<td><?php echo $sport[0] ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>

	<div class="sp_block_accord">
		<h3 class="sp_block_accord_title sp_event_rows_title">API ID Теннис турниры</h3>
		<div class="sp_block_accord_content">
			<div class="sp-table-wrapper">
				<table>
					<thead>
						<tr>
							<th>ID</th>
							<th>Название</th>
							<th>Категория</th>
							<th>Страна</th>
							<th>Начало</th>
							<th>Конец</th>
						</tr>
					</thead>					
					<?php foreach ( $tennis_feeds as $tennis ) : ?>
						<tr>
							<td><?php echo $tennis['id'] ?></td>
							<td><?php echo $tennis['name'] ?></td>
							<td><?php echo $tennis['category'] ?></td>
							<td><?php echo $tennis['country'] ?></td>
							<td><?php echo $tennis['start'] ?></td>
							<td><?php echo $tennis['end'] ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>			
		</div>
	</div>


</div>
		<?php endwhile;
	} ?>



<?php get_footer(); ?>
