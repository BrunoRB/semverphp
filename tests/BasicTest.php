<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use BrunoRB\SemverPHP;

class BasicTestTest extends TestCase {

	static public $data;

	static public function setUpBeforeClass() {
		self::$data = json_decode(file_get_contents(__DIR__ . '/test.json'), true);
	}

	public function testIsValid() {
		foreach(self::$data['valid'] as $v) {
			$this->assertTrue(SemverPHP::isValid($v), "$v should be valid");
		}
	}

	public function testIsInvalid() {
		foreach(self::$data['invalid'] as $v) {
			$this->assertFalse(SemverPHP::isValid($v), "$v should be invalid");
		}
	}

	public function testSplit() {
		foreach(self::$data['split'] as $v => $data) {
			$expected = [];
			foreach(['major', 'minor', 'patch', 'preRelease', 'buildMetadata'] as $i => $key) {
				$expected[$key] = $data[$i];
			}
			$this->assertEquals($expected, SemverPHP::split($v));
		}
	}

	public function testCompare() {
		$compare = self::$data['compare'];

		for ($i=0; $i<count($compare); $i++) {
			for ($j=$i+1; $j<count($compare); $j++) {
				$a = $compare[$i];
				$b = $compare[$j];
				$this->assertEquals(-1, SemverPHP::compare($a, $b), "$a < $b, should return -1");
			}
		}

		for ($i=count($compare) - 1; $i>=0; $i--) {
			for ($j=$i-1; $j>=0; $j--) {
				$a = $compare[$i];
				$b = $compare[$j];
				$this->assertEquals(1, SemverPHP::compare($a, $b), "$a > $b, should return 1");
			}
		}

		for ($i=0; $i<count($compare); $i++) {
			$a = $compare[$i];
			$this->assertEquals(0, SemverPHP::compare($a, $a), "$a == $a, should return 0");
		}
	}
}