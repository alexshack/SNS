<?php
class BonusesModel extends Model {

	public $bookmaker;

	static $primaryKey = 'post_id';

	static $modelMeta = BonusesMetaModel::class;

	static $tableKeys = [
		'post_id' => 0,
		'bookmaker_id' => '',
		'amount' => 0,
		'date_start' => '0000-00-00',
		'date_unlimited' => 0,
        'date_end' => '0000-00-00',
		'max_bonus' => 0,
		'max_bonus_currency' => 0,
		'min_bonus' => 0,
		'min_bonus_currency' => 0,
		'achievement' => '',
		'promocode' => ''
	];

	static $tableName = 'bonuses';

	static $bonusTypePriority = [313, 314];

	public $bonusTypeTerms = [];

	function __construct( $primary, array $meta = [] ) {

		parent::__construct( $primary, $meta );
		$types = (new BonusesMetaQuery('bsm'))->select([
			'post_id',
			'meta_key',
			'meta_value'
		])->where([
			'meta_key__in' => ['bonus_type'],
			'post_id__in' => $this->ID
		])->limit(1000)->get_results(1);

		// $this->bonus_types = [];

		// foreach ($types as $type) {
		// 	$this->bonusTypeTerms[$type->meta_value] = get_term_by('term_id', $type->meta_value, 'bonus_type');
		// 	$this->bonus_types[] = $type->meta_value;
		// }
		// $this->bonus_types = implode(',', $this->bonus_types);

		foreach (explode(',', $this->bonus_types) as $type) {

		 	$this->bonusTypeTerms[$type] = get_term_by('term_id', $type, 'bonus_type');

		}

		return $this;

	}

    function getImageURL() {

        $img = (isset($this->thumbnail) && $this->thumbnail) ? $this->thumbnail->getURL('270x200') : false;

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

	function getCurrencySymbol($slug) {

		$symbols = [
			'RUB' => '₽'
		];

		return isset($symbols[$slug]) ? $symbols[$slug] : $slug;

	}

	function getCurrency($key) {

		$key .= '_currency';

		if(isset($this->$key)) {

			return get_term_by('term_id', $this->$key, 'currencies', 'objects');

		}

		return false;

	}

	function getBonusValue() {

	    return $this->getValue();

    }

    function getMaxBonus() {

	    return $this->getMaxValue();

    }

    function getMinBonus() {

	    return $this->getMinValue();

    }

	function getValue() {

		$bonusValue = false;

		if($this->getMaxValue()) {

			$bonusValue = $this->getMaxValue();

		} else if($this->getAmountValue()) {

			$bonusValue = $this->getAmountValue();

		} else if($this->getMinValue()) {

			$bonusValue = $this->getMinValue();

		}

		return $bonusValue;

	}

	function getMaxValue() {

		if(isset($this->max_bonus) && $this->max_bonus) {

			return moneyFormat($this->max_bonus) . '&nbsp;' . $this->getCurrencySymbol($this->getCurrency('max_bonus')->name);

		}

		return false;

	}

	function getMinValue() {

		if(isset($this->min_bonus) && $this->min_bonus && $this->getCurrency('min_bonus')) {

			return moneyFormat($this->min_bonus) . '&nbsp;' . $this->getCurrencySymbol($this->getCurrency('min_bonus')->name);

		}

		return false;

	}

	function getAmountValue() {

		if(isset($this->amount) && $this->amount) {

			return 'Кешбэк +' . $this->amount . '%';

		}

		return false;

	}

	function isExclusive() {

		if(isset($this->achievement) && $this->achievement === 'Эксклюзив') {

			return true;

		}

		return false;

	}

	function isNew() {
		if(isset($this->achievement) && $this->achievement === 'New') {

			return true;

		}

		return false;
	}

	function isFinished() {

		return $this->date_end < date('Y-m-d') && intval($this->date_unlimited) === 0;

	}

	function getBonusType($returnName = true) {

	    $types = explode(',', $this->bonus_types);

        $typeID = $types[0];

	    if(in_array(313, $types)) {

            $typeID = 313;

        } else if(in_array(314, $types)) {

            $typeID = 314;

        }

        $term = get_term_by('term_id', $typeID, 'bonus_type', 'objects');

	    if($returnName) {

	        return str_replace(' бонус', '', $term->name);

        }

        return $term;

	}

	function getTypes() {

	    $types = [];

	    foreach (explode(',', $this->bonus_types) as $typeID) {

            $types[] = get_term_by('term_id', $typeID, 'bonus_type', 'objects');

        }

	    return $types;

    }

	function isType($typeID) {

	    $typesIDs = [];

	    foreach (explode(',', $this->bonus_types) as $type) {

	        $typesIDs[] = $type;

        }

	    return in_array($typeID, $typesIDs);

    }

	function getBonusText() {

		$before = 'Бонус';

		if($this->achievement === 'Эксклюзив') {

			$before = 'Эксклюзивный бонус ';

		}

		if($this->getMaxValue()) {

			return $before . ' до ' . $this->getMaxValue();

		} else if ($this->getAmountValue()) {

			return $this->getAmountValue();

		} else if($this->getMinValue()) {

			return $before . ' от ' . $this->getMinValue();

		}

		return $before . ' по ссылке';

	}

	static function getBest($limit = 4) {

        $bookmakersUnion = new BookmakersUnion();

        $bookmakers = $bookmakersUnion->getData($bookmakersUnion->getMainQuery()->where(['cupis' => 1])->limit($limit), ['thumbnail', 'bonus']);

        $bonuses = [];

        foreach ($bookmakers as $bookmaker) :

            $bonus = $bookmaker->bonus;

            if($bonus) {

                $bonus->bookmaker = $bookmaker;

                $bonuses[] = $bonus;

            }

            if(count($bonuses) >= $limit) {

                break;

            }

        endforeach;

        return Thumbnails::setup($bonuses);

    }


}
