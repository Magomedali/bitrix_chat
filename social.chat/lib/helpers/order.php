<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2012 Bitrix
 */
namespace Social\Chat\helpers;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
// use Bitrix\Sale\Internals;

Loc::loadMessages(__FILE__);

class OrderTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_sale_order';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		global $DB, $USER;

		$maxLock = intval(Main\Config\Option::get('sale','MAX_LOCK_TIME', 60));

		$userID = (is_object($USER) ? (int)$USER->getID() : 0);

		$connection = Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$lockStatusExpression = '';
		if ($DB->type == 'MYSQL')
		{
			$lockStatusExpression = "if(DATE_LOCK is null, 'green', if(DATE_ADD(DATE_LOCK, interval ".$maxLock." MINUTE)<now(), 'green', if(LOCKED_BY=".$userID.", 'yellow', 'red')))";
		}
		elseif ($DB->type == 'MSSQL')
		{
			$lockStatusExpression = "case when DATE_LOCK is null then 'green' else case when dateadd(minute, ".$maxLock.", DATE_LOCK)<getdate() then 'green' else case when LOCKED_BY=".$userID." then 'yellow' else 'red' end end end";
		}
		elseif ($DB->type == 'ORACLE')
		{
			$lockStatusExpression = "DECODE(DATE_LOCK, NULL, 'green', DECODE(SIGN(1440*(SYSDATE-DATE_LOCK)-".$maxLock."), 1, 'green', decode(LOCKED_BY,".$userID.",'yellow','red')))";
		}

		return array(
			new Main\Entity\IntegerField('ID',
				array(
					'autocomplete' => true,
					'primary' => true,
				)
			),

			new Main\Entity\StringField('LID'),

			new Main\Entity\StringField(
				'ACCOUNT_NUMBER',
				array(
					'size' => 100
				)
			),

			new Main\Entity\StringField('TRACKING_NUMBER'),

			new Main\Entity\IntegerField('PAY_SYSTEM_ID'),
			new Main\Entity\IntegerField('DELIVERY_ID'),

			new Main\Entity\DatetimeField('DATE_INSERT'),

			new Main\Entity\ExpressionField(
				'DATE_INSERT_SHORT',
				$DB->datetimeToDateFunction('%s'),
				array('DATE_INSERT'),
				array('data_type' => 'datetime')
			),

			new Main\Entity\ExpressionField(
				'DATE_INSERT_FORMAT',
				static::replaceDateTime(),
				array('DATE_INSERT'),
				array('data_type' => 'datetime')
			),

			new Main\Entity\DatetimeField('DATE_UPDATE'),

			new Main\Entity\ExpressionField(
				'DATE_UPDATE_SHORT',
				$DB->datetimeToDateFunction('%s'),
				array('DATE_UPDATE'),
				array('data_type' => 'datetime')
			),

			new Main\Entity\ExpressionField(
				'PRODUCTS_QUANT',
				'(SELECT  SUM(b_sale_basket.QUANTITY)
						FROM b_sale_basket
						WHERE b_sale_basket.ORDER_ID = %s)',
				array('ID')
			),

			new Main\Entity\StringField('PERSON_TYPE_ID'),

			new Main\Entity\IntegerField(
				'USER_ID',
				array(
					'required' => true
				)
			),

			new Main\Entity\ReferenceField(
				'USER',
				'\Bitrix\Main\User',
				array('=this.USER_ID' => 'ref.ID'),
				array('join_type' => 'INNER')
			),

			new Main\Entity\BooleanField(
				'PAYED',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),

			new Main\Entity\DatetimeField('DATE_PAYED'),

			new Main\Entity\IntegerField('EMP_PAYED_ID'),

			new Main\Entity\BooleanField(
				'DEDUCTED',
				array(
					'values' => array('N','Y'),
					'default_value' => 'N'
				)
			),
			new Main\Entity\DatetimeField('DATE_DEDUCTED'),

			new Main\Entity\IntegerField('EMP_DEDUCTED_ID'),

			new Main\Entity\StringField('REASON_UNDO_DEDUCTED'),

			new Main\Entity\StringField('STATUS_ID'),

			new Main\Entity\ReferenceField(
				'STATUS',
				'Bitrix\Sale\Internals\StatusLang',
				array(
					'=this.STATUS_ID' => 'ref.STATUS_ID',
					'=ref.LID' => array('?', LANGUAGE_ID)
				)
			),

			new Main\Entity\DatetimeField('DATE_STATUS'),

			new Main\Entity\ExpressionField(
				'DATE_STATUS_SHORT',
				$DB->datetimeToDateFunction('%s'),
				array('DATE_STATUS'),
				array('data_type' => 'datetime')
			),

			new Main\Entity\IntegerField('EMP_STATUS_ID'),

			new Main\Entity\ReferenceField(
				'EMP_STATUS_BY',
				'Bitrix\Main\User',
				array(
					'=this.EMP_STATUS_ID' => 'ref.ID'
				)
			),

			new Main\Entity\BooleanField(
				'MARKED',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),

			new Main\Entity\DatetimeField('DATE_MARKED'),

			new Main\Entity\IntegerField('EMP_MARKED_ID'),

			new Main\Entity\ReferenceField(
				'EMP_MARKED_BY',
				'Bitrix\Main\User',
				array(
					'=this.EMP_MARKED_ID' => 'ref.ID'
				)
			),


			new Main\Entity\StringField('REASON_MARKED'),

			new Main\Entity\FloatField(
				'PRICE_DELIVERY'
			),
			new Main\Entity\BooleanField(
				'ALLOW_DELIVERY',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),
			new Main\Entity\DatetimeField('DATE_ALLOW_DELIVERY'),

			new Main\Entity\IntegerField('EMP_ALLOW_DELIVERY_ID'),

			new Main\Entity\BooleanField(
				'RESERVED',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),

			new Main\Entity\FloatField(
				'PRICE',
				array(
					'default_value' => '0.0000'
				)
			),

			new Main\Entity\StringField(
				'CURRENCY',
				array(
					'required' => true,
					'size' => 3
				)
			),

			new Main\Entity\FloatField(
				'DISCOUNT_VALUE',
				array(
					'default_value' => '0.0000'
				)
			),

			new Main\Entity\ExpressionField(
				'DISCOUNT_ALL',
				"%s + (SELECT  SUM(b_sale_basket.DISCOUNT_PRICE)
						FROM b_sale_basket
						WHERE b_sale_basket.ORDER_ID = %s)",
				array('DISCOUNT_VALUE', 'ID')
			),

			new Main\Entity\FloatField('TAX_VALUE'),

			new Main\Entity\FloatField('SUM_PAID'),

			new Main\Entity\ExpressionField(
				'SUM_PAID_FORREP',
				'CASE WHEN %s = \'Y\' THEN %s ELSE %s END',
				array('PAYED', 'PRICE', 'SUM_PAID')
			),

			new Main\Entity\StringField(
				'USER_DESCRIPTION',
				array(
					'size' => 2000
				)
			),

			new Main\Entity\StringField(
				'PAY_VOUCHER_NUM',
				array(
					'size' => 20,
				)
			),

			new Main\Entity\DateField('PAY_VOUCHER_DATE'),

			new Main\Entity\StringField('ADDITIONAL_INFO'),

			new Main\Entity\StringField('COMMENTS'),

			new Main\Entity\IntegerField('COMPANY_ID'),

			new Main\Entity\IntegerField('CREATED_BY'),

			new Main\Entity\ReferenceField(
				'CREATED_USER',
				'Bitrix\Main\User',
				array(
					'=this.CREATED_BY' => 'ref.ID'
				)
			),

			new Main\Entity\IntegerField('RESPONSIBLE_ID'),

			new Main\Entity\ReferenceField(
				'RESPONSIBLE_BY',
				'Bitrix\Main\User',
				array(
					'=this.RESPONSIBLE_ID' => 'ref.ID'
				)
			),

			new Main\Entity\StringField('STAT_GID'),

			new Main\Entity\DateField('DATE_PAY_BEFORE'),

			new Main\Entity\DateField('DATE_BILL'),

			new Main\Entity\BooleanField(
				'IS_RECURRING',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),

			new Main\Entity\IntegerField('RECURRING_ID'),

			new Main\Entity\IntegerField('LOCKED_BY'),

			new Main\Entity\ReferenceField(
				'LOCK_USER',
				'Bitrix\Main\User',
				array(
					'=this.LOCKED_BY' => 'ref.ID'
				)
			),

			new Main\Entity\DatetimeField('DATE_LOCK'),



			new Main\Entity\ExpressionField(
				'LOCK_USER_NAME',
				$helper->getConcatFunction("'('", "%s", "') '", "%s", "' '", "%s"),
				array('LOCK_USER.LOGIN', 'LOCK_USER.NAME', 'LOCK_USER.LAST_NAME')
				),

			new Main\Entity\ExpressionField(
				'LOCK_STATUS',
				$lockStatusExpression
			),

			new Main\Entity\ReferenceField(
				'USER_GROUP',
				'Bitrix\Main\UserGroup',
				array(
					'=ref.USER_ID' => 'this.USER_ID'
				)
			),

			new Main\Entity\ReferenceField(
				'RESPONSIBLE',
				'Bitrix\Main\User',
				array(
					'=this.RESPONSIBLE_ID' => 'ref.ID'
				)
			),

			new Main\Entity\ReferenceField(
				'BASKET',
				'Bitrix\Sale\Internals\Basket',
				array(
					'=this.ID' => 'ref.ORDER_ID'
				),
				array('join_type' => 'INNER')

			),

			new Main\Entity\ExpressionField(
				'BASKET_PRICE_TOTAL',
				'(%s * %s)',
				array('BASKET.PRICE', 'BASKET.QUANTITY')
			),

			new Main\Entity\ReferenceField(
				'PAYMENT',
				'Bitrix\Sale\Internals\Payment',
				array(
					'=ref.ORDER_ID' => 'this.ID',
				)
			),

			new Main\Entity\ReferenceField(
				'SHIPMENT',
				'Bitrix\Sale\Internals\Shipment',
				array(
					'=ref.ORDER_ID' => 'this.ID',
				)
			),

			new Main\Entity\ReferenceField(
				'PROPERTY',
				'Bitrix\Sale\Internals\OrderPropsValue',
				array(
					'=ref.ORDER_ID' => 'this.ID',
				),
				array('join_type' => 'INNER')
			),

			new Main\Entity\BooleanField(
				'RECOUNT_FLAG',
				array(
					'values' => array('N', 'Y')
				)
			),

			new Main\Entity\IntegerField('AFFILIATE_ID'),

			new Main\Entity\StringField(
				'DELIVERY_DOC_NUM',
				array(
					'size' => 20
				)
			),

			new Main\Entity\DatetimeField('DELIVERY_DOC_DATE'),

			new Main\Entity\BooleanField(
				'UPDATED_1C',
				array(
					'values' => array('N', 'Y'),
					'default' => 'N'
				)
			),

			new Main\Entity\StringField('ORDER_TOPIC'),

			new Main\Entity\StringField('XML_ID'),

			new Main\Entity\StringField('ID_1C'),

			new Main\Entity\StringField('VERSION_1C'),

			new Main\Entity\IntegerField('VERSION'),

			new Main\Entity\BooleanField(
				'EXTERNAL_ORDER',
				array(
					'values' => array('N', 'Y')
				)
			),

			new Main\Entity\IntegerField('STORE_ID'),

			new Main\Entity\BooleanField(
				'CANCELED',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),
			new Main\Entity\IntegerField('EMP_CANCELED_ID'),

			new Main\Entity\ReferenceField(
				'EMP_CANCELED_BY',
				'Bitrix\Main\User',
				array(
					'=this.EMP_CANCELED_ID' => 'ref.ID'
				)
			),

			new Main\Entity\DatetimeField('DATE_CANCELED'),

			new Main\Entity\ExpressionField(
				'DATE_CANCELED_SHORT',
				$DB->datetimeToDateFunction('%s'),
				array('DATE_CANCELED'),
				array('DATA_TYPE' => 'datetime')
			),

			new Main\Entity\StringField('REASON_CANCELED'),


			new Main\Entity\StringField('BX_USER_ID'),

			new Main\Entity\BooleanField(
				'RUNNING',
				array(
					'values' => array('N', 'Y'),
					'default_value' => 'N'
				)
			),

			new Main\Entity\ReferenceField(
				'ORDER_COUPONS',
				'Bitrix\Sale\Internals\OrderCoupons',
				array(
					'=ref.ORDER_ID' => 'this.ID',
				),
				array('join_type' => 'LEFT')
			),

			new Main\Entity\ReferenceField(
				'ORDER_DISCOUNT_DATA',
				'Bitrix\Sale\Internals\OrderDiscountData',
				array(
					'=ref.ORDER_ID' => 'this.ID',
					'=ref.ENTITY_TYPE' => new Main\DB\SqlExpression('?', OrderDiscountDataTable::ENTITY_TYPE_ORDER)
				),
				array('join_type' => 'LEFT')
			),

			new Main\Entity\ExpressionField(
				'BY_RECOMMENDATION',
				"(SELECT (CASE WHEN MAX(BR.RECOMMENDATION) IS NULL OR MAX(BR.RECOMMENDATION) = '' THEN 'N' ELSE 'Y' END) FROM b_sale_basket BR WHERE BR.ORDER_ID=%s GROUP BY BR.ORDER_ID)",
				array('ID')
			)
		);
	}

	public static function getUfId()
	{
		return 'ORDER';
	}

	protected static function replaceDateTime()
	{
		global $DB;
		$datetime = $DB->DateToCharFunction('___DATETIME___');
		$datetime = str_replace('%', '%%', $datetime);
		$datetime = str_replace('___DATETIME___', '%1$s', $datetime);
		return $datetime;
	}
}



class OrderDiscountDataTable extends Main\Entity\DataManager
{
	const ENTITY_TYPE_BASKET = 0x0001;
	const ENTITY_TYPE_DELIVERY = 0x0002;
	const ENTITY_TYPE_SHIPMENT = 0x0004;
	const ENTITY_TYPE_DISCOUNT = 0x0008;
	const ENTITY_TYPE_ORDER = 0x0010;
	const ENTITY_TYPE_ROUND = 0x0020;
	const ENTITY_TYPE_DISCOUNT_STORED_DATA = 0x0040;

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_sale_order_discount_data';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => new Main\Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('ORDER_DISCOUNT_DATA_ENTITY_ID_FIELD')
			)),
			'ORDER_ID' => new Main\Entity\IntegerField('ORDER_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ORDER_DISCOUNT_DATA_ENTITY_ORDER_ID_FIELD')
			)),
			'ENTITY_TYPE' => new Main\Entity\EnumField('ENTITY_TYPE', array(
				'required' => true,
				'values' => array(
					self::ENTITY_TYPE_BASKET,
					self::ENTITY_TYPE_DELIVERY,
					self::ENTITY_TYPE_SHIPMENT,
					self::ENTITY_TYPE_DISCOUNT,
					self::ENTITY_TYPE_ORDER,
					self::ENTITY_TYPE_ROUND,
					self::ENTITY_TYPE_DISCOUNT_STORED_DATA
				),
				'title' => Loc::getMessage('ORDER_DISCOUNT_DATA_ENTITY_ENTITY_TYPE_FIELD')
			)),
			'ENTITY_ID' => new Main\Entity\IntegerField('ENTITY_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ORDER_DISCOUNT_DATA_ENTITY_ENTITY_ID_FIELD')
			)),
			'ENTITY_VALUE' => new Main\Entity\StringField('ENTITY_VALUE', array(
				'validation' => array(__CLASS__, 'validateEntityValue'),
				'title' => Loc::getMessage('ORDER_DISCOUNT_DATA_ENTITY_ENTITY_VALUE_FIELD')
			)),
			'ENTITY_DATA' => new Main\Entity\TextField('ENTITY_DATA', array(
				'required' => true,
				'serialized' => true,
				'title' => Loc::getMessage('ORDER_DISCOUNT_DATA_ENTITY_ENTITY_DATA_FIELD')
			))
		);
	}
	/**
	 * Returns validators for ENTITY_VALUE field.
	 *
	 * @return array
	 */
	public static function validateEntityValue()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}

	/**
	 * Upsert basket item data.
	 *
	 * @param int $order				Order id.
	 * @param int $basket				Basket id.
	 * @param array $data				Data list.
	 * @param bool $clear				Clear old values or update.
	 * @return bool
	 */
	public static function saveBasketItemData($order, $basket, $data, $clear = false)
	{
		$order = (int)$order;
		$basket = (int)$basket;
		if ($order < 0 || $basket <= 0 || empty($data) || !is_array($data))
			return false;
		$clear = ($clear === true);
		$id = 0;
		$fields = array(
			'ENTITY_DATA' => $data
		);
		$dataIterator = self::getList(array(
			'select' => array('ID', 'ENTITY_DATA'),
			'filter' => array('=ORDER_ID' => $order, '=ENTITY_TYPE' => self::ENTITY_TYPE_BASKET, '=ENTITY_ID' => $basket)
		));
		if ($oldData = $dataIterator->fetch())
		{
			if (!$clear && !empty($oldData['ENTITY_DATA']))
				$fields['ENTITY_DATA'] = array_merge($oldData['ENTITY_DATA'], $fields['ENTITY_DATA']);
			$id = (int)$oldData['ID'];
		}
		unset($oldData, $dataIterator);
		if ($id > 0)
		{
			$result = self::update($id, $fields);
		}
		else
		{
			$fields['ORDER_ID'] = $order;
			$fields['ENTITY_TYPE'] = self::ENTITY_TYPE_BASKET;
			$fields['ENTITY_ID'] = $basket;
			$fields['ENTITY_VALUE'] = $basket;
			$result = self::add($fields);
			if ($result->isSuccess())
				$id = (int)$result->getId();
		}
		unset($fields, $id);
		return $result->isSuccess();
	}

	/**
	 * Clear data for basket item.
	 *
	 * @param int $basket			Basket id.
	 * @return bool
	 */
	public static function clearByBasketItem($basket)
	{
		$basket = (int)$basket;
		if ($basket <= 0)
			return false;

		$conn = Main\Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->queryExecute(
			'delete from '.$helper->quote(self::getTableName()).
			' where '.$helper->quote('ENTITY_TYPE').' = '.self::ENTITY_TYPE_BASKET.
			' and '.$helper->quote('ENTITY_ID').' = '.$basket
		);
		unset($helper, $conn);
		return true;
	}

	/**
	 * Delete data by order.
	 *
	 * @param int $order		Order id.
	 * @return bool
	 */
	public static function clearByOrder($order)
	{
		$order = (int)$order;
		if ($order <= 0)
			return false;

		$conn = Main\Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->queryExecute('delete from '.$helper->quote(self::getTableName()).' where '.$helper->quote('ORDER_ID').' = '.$order);
		unset($helper, $conn);

		return true;
	}

	/**
	 * Clear data by discount list.
	 *
	 * @param array|int $discountList			Discount ids list.
	 * @return bool
	 */
	public static function clearByDiscount($discountList)
	{
		if (!is_array($discountList))
			$discountList = array($discountList);
		if (empty($discountList))
			return false;
		Main\Type\Collection::normalizeArrayValuesByInt($discountList, true);
		if (empty($discountList))
			return false;

		$conn = Main\Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->queryExecute(
			'delete from '.$helper->quote(self::getTableName()).
			' where '.$helper->quote('ENTITY_TYPE').' = '.self::ENTITY_TYPE_DISCOUNT.
			' and '.$helper->quote('ENTITY_ID').' in ('.implode(',', $discountList).')'
		);
		unset($helper, $conn);

		return true;
	}
}