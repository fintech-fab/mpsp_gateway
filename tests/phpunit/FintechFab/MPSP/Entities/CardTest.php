<?php
use FintechFab\MPSP\Entities\Card;

/**
 * @property \FintechFab\MPSP\Entities\Card $card
 */
class TransferCardTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		$this->card = new Card;
	}

	public function testOffsetExists()
	{
		$this->card->offsetSet('bEnabled', 'sss');

		// существует
		$exists = $this->card->offsetExists('bEnabled');
		$this->assertTrue($exists);

		// не существует
		$exists = $this->card->offsetExists('zzz');
		$this->assertFalse($exists);
	}

	public function testOffsetGet()
	{
		$this->card->offsetSet('enabled', true);

		$result = $this->card->offsetGet('enabled');

		$this->assertTrue($result);

		// не существует
		$result = $this->card->offsetGet('zzz');

		$this->assertFalse($result);
	}

	public function testOffsetUnSet()
	{
		$this->card->offsetSet('bEnabled', true);

		$this->card->offsetUnset('bEnabled');

		$this->assertFalse($this->card->offsetExists('bEnabled'));
	}

	public function testDoImport()
	{
		Crypt::shouldReceive('decrypt')->with('E1')->andReturn('D1')->once();
		Crypt::shouldReceive('decrypt')->with('E2')->andReturn('D2')->once();
		Crypt::shouldReceive('decrypt')->with('E3')->andReturn('D3')->once();
		Crypt::shouldReceive('decrypt')->with('E4')->andReturn('D4')->once();

		$params = [
			'card'         => 'E1',
			'expire_year'  => 'E2',
			'expire_month' => 'E3',
			'cvv'          => 'E4',
		];

		$this->card->doImport($params);

		$this->assertEquals('D1', $this->card->number);
		$this->assertEquals('D2', $this->card->expire_year);
		$this->assertEquals('D3', $this->card->expire_month);
		$this->assertEquals('D4', $this->card->cvv);
	}

}