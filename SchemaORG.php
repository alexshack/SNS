<?php
class SchemaORG {
	protected $item;
	public $schema;

	function __construct($item, $type) {
		$this->item   = $item;
		$this->schema = $this->getSchema($type);
	    return $this;
	}

	function getSchema($type) {
		if ($type) {
			switch ($type) {
			    case 'bonus':
			        $schema = $this->getSchemaBonus();
			        break;
			    case 'predict':
			        $schema = $this->getSchemaPredict();
			        break;
			    case 'bookmaker':
			        $schema = $this->getSchemaBookmaker();
			        break;
			    case 'news':
			        $schema = $this->getSchemaNews();
			        break;			        
			    case 'breadcrumbs':
			    	$schema = $this->getSchemaBreadcrumbs();
			    	break;
			    case 'comment':
			    	$schema = $this->getSchemaComment($this->item);
			    	break;			    	
			}
			return $this->prepareSchema($schema);
			//return $schema;
		}
		return false;
    }

    function getSchemaBonus() {
    	$schema = [
    		'@context'            => 'https://schema.org',
    		'@type'               => 'SaleEvent',
    		'startDate'           => $this->formatDate(get_the_date( 'd.m.Y', $this->item->post_id)),
    		'endDate'             => $this->item->date_unlimited == 1 ? $this->formatDate('+1 year') : $this->formatDate($this->item->date_end),
    		'name'                => $this->item->post_title,
    		'description'         => $this->formatText($this->item->post_content),
    		'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
    		'eventStatus'         => 'https://schema.org/EventScheduled',
    		'image'				  => $this->item->getImageURL(),
    		'url'                 => $this->formatUrl($this->item->bookmaker->getPartnerLink()),
    		'location'            => [
    			[
	    			'@type' => 'VirtualLocation',
	    			'name'  => 'Сайт ' . $this->item->bookmaker->name,
	    			'url'   => $this->formatUrl($this->item->bookmaker->getPartnerLink()),
	    		],			
    		],
    		'organizer' => [
    			'@type' => 'Organization',
    			'name'  => $this->item->bookmaker->post_title,
        		'url'   => $this->formatUrl($this->item->bookmaker->getPartnerLink()),
        		'logo'  => $this->item->bookmaker->thumbnail->getUrl('100x100', 'full')
    		]

    	];
 
    	return $schema;

    }

    function getSchemaNews() {
    	$schema = [
    		'@context'            => 'https://schema.org',
    		'@type'               => 'NewsArticle',
    		'headline'            => $this->item->getTitle(),
    		'datePublished'       => $this->item->getDate('c'),
    		'dateModified'        => $this->getModifiedDate(strtotime($this->item->getDate('Y-m-d'))),
    		'url'                 => get_permalink($this->item->ID),
    		'image'				  => [
    			wp_get_attachment_image_src( get_post_thumbnail_id( $this->item->ID ), 'full' )[0],
    		],
    		'author'              => $this->getSchemaAuthor($this->item->post_author)
    	];

    	$comments = get_comments( [
			'post_id' => $this->item->ID,
		] );

    	if (count($comments)) {
    		$schema['comment'] = [];
    		foreach ($comments as $comment) {
    			$schema['comment'][] = $this->getSchemaComment($comment);
    		}
    	}

     	return $schema;

    }

    function getSchemaBreadcrumbs() {
    	$schema = [
    		'@context'            => 'https://schema.org',
    		'@type'               => 'BreadcrumbList',
    		'itemListElement'     => []
    	];
 
    	foreach ($this->item as $i => $item) {
    		$schema['itemListElement'][] = [
    			'@type' => 'ListItem',
    			'position' => $i + 1,
    			'name' => $item['name'],
    			'item' => $item['link']
    		];
    		if (isset($item['self']) && $item['self']) {
    			unset($schema['itemListElement'][$i]['item']);
    		}
    	}

    	return $schema;

    }

	function getSchemaComment($comment) {
		$schema = [
			'@context'      => 'https://schema.org',
			'@type'         => 'Comment',
			'text'          => $comment->comment_content,
			'datePublished' => $this->formatDate($comment->comment_date),
			'author'        => $this->getSchemaAuthor($comment->user_id),
		];
		return $schema;
	}

	function getSchemaAuthor($author_id) {
		$schema = [
			'@type' => 'Person',
			'name'  => $this->getAuthorName($author_id),
			'url'   => get_author_posts_url( $author_id )
		];
		return $schema;
	}


    function prepareSchema($schema) {
    	if ($schema) {
    		$result  = '<script type="application/ld+json">';
    		$result .= json_encode($schema, JSON_UNESCAPED_UNICODE);
    		$result .= '</script>';
    		return $result;
    	}
    	return false;
    }

	function getModifiedDate($post_date) {
		$last_update_date = get_post_meta($this->item->ID, 'last_update_date', true );
		if ( ( $post_date < strtotime( '-4 months' ) ) && $last_update_date ):
			return $this->formatDate( $last_update_date );
		endif;
		return wp_date('c', $post_date);
    }

    function formatDate($date) {
    	return wp_date( 'c', strtotime( $date ) );
    }

    function formatText($text) {
    	$full_text = do_shortcode($text);
    	return wp_strip_all_tags($full_text, true);
    }

    function formatUrl($url) {
    	return str_replace('" data-status="blocked', '', $url);
    }     




	protected function getAuthorName($author_id) {
		$author_name = get_the_author_meta('display_name', $author_id);
		if($author_name) {
			return $author_name;
		}
		return get_the_author_meta('nickname', $author_id);
	}

}
