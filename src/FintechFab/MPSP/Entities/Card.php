<?php namespace FintechFab\MPSP\Entities;

use ArrayAccess;
use Crypt;

/**
 * @property string $number
 * @property int    $expire_month
 * @property int    $expire_year
 * @property int    $cvv
 */
class Card implements ArrayAccess
{

	private $attributes = [];

	public function offsetUnset($offset)
	{
		unset($this->attributes[$offset]);
	}

	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	public function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->attributes[$offset] : false;
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->attributes);
	}

	public function offsetSet($offset, $value)
	{
		$this->attributes[$offset] = $value;
	}

	public function doImport(array $encryptedData)
	{
		$decryptedData = [
			'number'       => Crypt::decrypt($encryptedData['card']),
			'expire_year'  => Crypt::decrypt($encryptedData['expire_year']),
			'expire_month' => Crypt::decrypt($encryptedData['expire_month']),
			'cvv'          => Crypt::decrypt($encryptedData['cvv']),
		];

		$this->attributes = $decryptedData;
	}

}