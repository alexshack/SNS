<?php 
$bookmaker = Bookmaker::setup_all($game['game_bk_id']); 
$live_tag = '<div class="cyber_game_live">LIVE</div>';
if (! empty($game['game_stream_url'])) {
	$live_tag = '<a href="' . $game['game_stream_url'] . '" class="cyber_game_live" title="' . $game['game_stream'] . '" target="_blank" rel="nofollow">LIVE</a>';
}

?>

<div class="cyber_game" game_id="<?php echo $game_id; ?>">
	<div class="cyber_game_wrapper">
		<div class="cyber_game_header">
			<div class="cyber_game_sport">
				<img class="cyber_game_sport_img lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $game['sport_img']; ?>" width="32" height="32" alt="<?php echo $game['sport_name']; ?>" title="<?php echo $game['sport_name']; ?>">
				<div class="cyber_game_sport_name"><?php echo $game['tournament_name']; ?></div>
			</div>
			<?php if ($game['game_type'] == 'live') : ?>
				<?php echo $live_tag; ?>
			<?php else : ?>
				<span class="cyber_game_time"><?php echo $game['game_date']; ?></span>
			<?php endif; ?>
		</div>
		<div class="cyber_game_teams">
			<div class="cyber_game_team">
				<div class="cyber_game_sport">
					<?php if($game['game_home_img']) {
						$team_img = $game['game_home_img'];
					} else {
						$team_img = $game['sport_img'];
					}

					?>
					<img class="cyber_game_team_img lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $team_img; ?>" width="32" height="32" alt="<?php echo $game['game_home_name']; ?>" title="<?php echo $game['game_home_name']; ?>">
					<div class="cyber_game_team_name"><?php echo $game['game_home_name']; ?></div>
				</div>
				<div class="cyber_game_result">
					<?php if ($game['game_type'] == 'live') : ?>
						<span class="cyber_game_score"><?php echo $game['game_home_score']; ?></span>
					<?php endif; ?>
					<a class="cyber_game_kf" href="<?php echo $game['game_bk_link']; ?>" title="<?php echo $game['game_home_kf_name']; ?>" target="_blank" rel="nofollow"><span>КФ</span><span><?php echo $game['game_home_kf']; ?></span></a>
				</div>
			</div>
			<div class="cyber_game_team">
				<div class="cyber_game_sport">
					<?php if($game['game_away_img']) {
						$team_img = $game['game_away_img'];
					} else {
						$team_img = $game['sport_img'];
					}

					?>
					<img class="cyber_game_team_img lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $team_img; ?>" width="32" height="32" alt="<?php echo $game['game_away_name']; ?>" title="<?php echo $game['game_away_name']; ?>">
					<div class="cyber_game_team_name"><?php echo $game['game_away_name']; ?></div>
				</div>
				<div class="cyber_game_result">
					<?php if ($game['game_type'] == 'live') : ?>
						<span class="cyber_game_score"><?php echo $game['game_away_score']; ?></span>
					<?php endif; ?>
					<a class="cyber_game_kf" href="<?php echo $game['game_bk_link']; ?>" title="<?php echo $game['game_away_kf_name']; ?>" target="_blank" rel="nofollow"><span>КФ</span><span><?php echo $game['game_away_kf']; ?></span></a>
				</div>
			</div>		
		</div>
		<div class="cyber_game_footer">
			<a href="<?php echo $game['game_bk_link']; ?>" class="cyber_game_btn" target="_blank" rel="nofollow">Поставить</a>
			<a href="<?php echo $bookmaker->getPartnerLink(); ?>" class="" target="_blank" rel="nofollow">
				<?php echo $bookmaker->thumbnail->getLazyLoadImg('cyber_game_logo', ['alt' => ''], '195x50') ?>
			</a>
		</div>
	</div>

</div>