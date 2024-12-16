<?php
class BonusesMetaModel extends ModelMeta {

	static $primaryKey = 'mid';

	static $singleMetaKeys = [];

	static $multiMetaKeys = [
		'bonus_type'
	];

	static $tableName = 'bonuses_meta';

}
