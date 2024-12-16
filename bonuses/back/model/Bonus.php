<?php
class Bonus extends PostAbstract {

	public $bonus_types, $terms, $promocode, $promo_attr;

	function __construct($post) {
		parent::__construct($post);
		$this->promocode = $this->setPromoCode();
		$this->promo_attr = $this->setPromoAttr();
		return $this;
	}

	function getPromoCode() {
	    return $this->promocode;
    }

	function setPromoCode() {
		if ($this->post->promocode) {
			return $this->post->promocode;
		} else {
			if (isset($this->bookmaker->promocode)) {
				return $this->bookmaker->promocode;
			}
		}
	    return false;
    }

    function getPromoAttr() {

	    return $this->promo_attr;

    }
	function setPromoAttr() {
	    if(!$this->promocode) {
	        return false;
	    }
		
	    $gif_id_d = get_post_meta( $this->post->ID, 'promo_desktop', true );
	    if (empty($gif_id_d) && isset($this->bookmaker->promo_data->options['gif_desktop'])) {
	    	$gif_id_d = $this->bookmaker->promo_data->options['gif_desktop'];
	    }

	    $gif_id_m = get_post_meta( $this->post->ID, 'promo_mobile', true );
	    if (empty($gif_id_m) && isset($this->bookmaker->promo_data->options['gif_desktop'])) {
	    	$gif_id_m = $this->bookmaker->promo_data->options['gif_mobile'];
	    }

		$gif_desktop = wp_get_attachment_image_url( $gif_id_d, 'full' );
		$gif_mobile  = wp_get_attachment_image_url( $gif_id_m, 'full' );;
		$promocode   = $this->promocode;
		$popup_time  = 7;
		$gif         = ( wp_is_mobile() ) ? $gif_mobile : $gif_desktop;
		$bk          = get_post_meta( $this->post->bookmaker_id, 'bm_main_name', true );
		if ( ! test_clear() ) {
			$attr[]      = 'data-promoimg="' . $gif . '"';
		}
		$attr[]      = 'data-promotime="' . $popup_time . '"';
		$attr[]      = 'data-promocode="' . $promocode . '"';
		$attr[]      = 'data-bkname="' . $bk . '"';

		$is_cupys = get_post_meta( $this->post->bookmaker_id, '_cupys_meta_key', true );
		$attr[]   = $is_cupys === 'true' ? 'data-link="' . get_bonus_permalink( $this->post->bookmaker_id ) . '"' : '';

		return implode( ' ', $attr );
	}

	function getMinBonusCurrency() {
		if(isset($this->post->min_bonus_currency) && is_array($this->terms) && isset($this->terms[$this->post->min_bonus_currency])) {
			return $this->terms[$this->post->min_bonus_currency];
		}
		return false;
	}

	function getMaxBonusCurrency() {
		if(isset($this->post->max_bonus_currency) && is_array($this->terms) && isset($this->terms[$this->post->max_bonus_currency])) {
			return $this->terms[$this->post->max_bonus_currency];
		}
		return false;
	}

	function getBonusType($property = 'name') {
		$this->sortBonusTypes();
		if(is_array($this->terms) && is_array($this->bonus_types) && count($this->bonus_types) && isset($this->terms[$this->bonus_types[0]])) {
			return str_replace(' бонус', '', $this->terms[$this->bonus_types[0]]->$property);
		}
		return false;
	}

	function getTypes() {
		$this->sortBonusTypes();
		$types = [];
		if(is_array($this->terms) && is_array($this->bonus_types) && count($this->bonus_types) && isset($this->terms[$this->bonus_types[0]])) {
			foreach ($this->bonus_types as $bonus_type) {
				$types[] = $this->terms[$bonus_type];
			}
		}
		return $types;
	}

    function isType($typeID) {

        $typesIDs = [];

        foreach ($this->bonus_types as $type) {

            $typesIDs[] = $type;

        }

        return in_array($typeID, $typesIDs);

    }

	protected function sortBonusTypes() {

		$bonus_types = [];
		if(is_array($this->bonus_types)) {
			if(in_array(313, $this->bonus_types)) {
				$bonus_types[] = 313;
			}
			if(in_array(314, $this->bonus_types)) {
				$bonus_types[] = 314;
			}
			foreach ($this->bonus_types as $bonus_type) {
				if(!in_array($bonus_type, [314, 313])) {
					$bonus_types[] = $bonus_type;
				}
			}
		}
		$this->bonus_types = $bonus_types;
		return $this;
	}

	function isExclusive() {
		return $this->post->achievement === 'Эксклюзив';
	}
	function isNew() {
		return $this->post->achievement === 'New';
	}
	function isFinished() {
		return $this->post->date_end < date('Y-m-d') && intval($this->post->date_unlimited) === 0;
	}

	function getBonusValue() {
		$bonus = false;
		if($this->post->max_bonus) {
			$bonus = moneyFormat($this->post->max_bonus) . '&nbsp;' . $this->getCurrencySymbol($this->terms[$this->post->max_bonus_currency]->name);
		} else if($this->post->amount) {
			$bonus = 'Кешбэк +' . $this->post->amount . '%';
		} else if($this->post->min_bonus) {
			$bonus = moneyFormat($this->post->min_bonus) . '&nbsp;' . $this->getCurrencySymbol($this->terms[$this->post->min_bonus_currency]->name);
		}
		return $bonus;
	}



	function getCurrencySymbol($slug) {

		$symbols = [
			'RUB' => '₽'
		];

		return isset($symbols[$slug]) ? $symbols[$slug] : $slug;

	}

	function getMaxBonus() {
		$bonus = false;
		if($this->post->max_bonus) {
			$bonus = moneyFormat($this->post->max_bonus) . '&nbsp;' . $this->terms[$this->post->max_bonus_currency]->name;
		}
		return $bonus;
	}

	function getMinBonus() {
		$bonus = false;
		if($this->post->min_bonus) {
			$bonus = moneyFormat($this->post->min_bonus) . '&nbsp;' . $this->terms[$this->post->min_bonus_currency]->name;
		}
		return $bonus;
	}

	function getBonusCurrency($prefix = 'max') {
		$property_name = $prefix . '_bonus_currency';
		return $this->terms[$this->post->$property_name]->name;
	}

	function getBonusText() {
		$before = 'Бонус';
		if($this->post->achievement === 'Эксклюзив') {
			$before = 'Эксклюзивный бонус ';
		}
		if($this->post->max_bonus) {
			return $before . ' до ' . moneyFormat($this->post->max_bonus) . '&nbsp;' . $this->terms[$this->post->max_bonus_currency]->name;
		} else if ($this->post->amount) {
			return 'Кешбэк +' . $this->post->amount . '%';
		} else if($this->post->min_bonus) {
			return $before . ' от ' . moneyFormat($this->post->min_bonus) . '&nbsp;' . $this->terms[$this->post->min_bonus_currency]->name;
		}
		return $before . ' по ссылке';
	}

	function linkMarkup($text = '', $attr = []) {
		return '<a ' . $this->arrayToAttributes($attr) . ' href="' . $this->getPermalink() . '" alt="Обзор ' . $this->post->name . '">' . $text . '</a>';
	}

	function getImageURL() {
		$img = (isset($this->post->thumbnail) && $this->post->thumbnail) ? $this->post->thumbnail->getURL('270x200') : false;
		if(!$img) {
			if(intval($this->getBonusType('term_id')) === 313) {
				$img = get_template_directory_uri() . '/img/default-bonus-image-1.jpg';
			} elseif(intval($this->getBonusType('term_id')) === 314) {
				$img = get_template_directory_uri() . '/img/default-bonus-image-2.jpg';
			} else {
				$img = get_template_directory_uri() . '/img/default-bonus-image-3.jpg';
			}
		}
		return $img;
	}

	function getFullImageURL() {
		$img = (isset($this->post->thumbnail) && $this->post->thumbnail) ? $this->post->thumbnail->getURL('full') : false;
		if(!$img) {
			$img = get_template_directory_uri() . '/img/bonus-default.jpg';
		}
		return $img;
	}

	function getDateEnd() {
		$date = false;
		if($this->post->date_unlimited) {
			$date = 'Бессрочно';
		} else {
			if($this->post->date_end) {
				$date = date('d.m.Y', strtotime($this->post->date_end));
			}
		}
		return $date;
	}

	function getBookmakerId() {
		return $this->post->bookmaker_id;
	}

	function getBonusId() {
		return $this->post->ID;
	}
}
