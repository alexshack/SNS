<tr class="<?php echo $position > 10 ? 'more-hidden' : ''; ?>">
	<td><?php echo $position; ?></td>
	<td class="olympics_medals-country">
		<div class="olympics_medals-country-inside">
			<?php if ( $controller->hasCountryFlag( $medal->country_id ) ): ?>
                <img class="olympics_medals-country-image"
                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                     data-src="<?php print get_template_directory_uri(); ?>/img/country/<?php print $controller->getCountryFlag( $medal->country_id ); ?>.svg"
                     width="30"
                     height="20"
                     alt="<?php echo $medal->country_name; ?>">
			<?php endif; ?>
			<span class="olympics__medals-country-text"><?php echo $medal->country_name; ?></span>
		</div>
	</td>
	<td><?php echo $medal->gold; ?></td>
	<td><?php echo $medal->silver; ?></td>
	<td><?php echo $medal->bronze; ?></td>
	<td class="olympics_medals-count-total"><?php echo $medal->amount; ?></td>
</tr>
