<?php

declare(strict_types=1);

namespace BrunoRB;

class SemverPHP {

	const PATTERN = '/^' .
		// major-minor-patch https://semver.org/#spec-item-2
		'(\d|[1-9]\d*)\.(\d|[1-9]\d*)\.(\d|[1-9]\d*)' .
		// pre-release https://semver.org/#spec-item-9
		'(-(?:' .
			'(?:0|[1-9][0-9A-Za-z-]*|[A-Za-z-][0-9A-Za-z-]*)' .
			'(?:\.(?:[1-9][0-9A-Za-z-]*|[A-Za-z-][0-9A-Za-z-]*|0))*' .
		'))?' .
		// build-metadata https://semver.org/#spec-item-10
		'(\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?' .
		'$/';

	/**
	 * @param string $semver https://semver.org/#summary
	 * @return bool
	 */
	static public function isValid(string $semver): bool {
		return preg_match(self::PATTERN, $semver) === 1;
	}

	/**
	 * @param string $semver https://semver.org/#summary
	 *
	 * @return array [
	 * 	'major' => 'version',
	 * 	'minor' => 'version',
	 *  'patch' => 'version',
	 *  'preRelease' => 'version' || null,
	 *  'buildMetadata' => 'version' || null,
	 * ]
	 *
	 * @throws Exception for invalid $semver
	 */
	static public function split(string $semver): array {
		$res = preg_match(self::PATTERN, $semver, $m);
		if ($res !== 1) {
			throw new Exception("Invalid semver $semver");
		}

		return [
			'major' => $m[1],
			'minor' => $m[2],
			'patch' => $m[3],
			'preRelease' => count($m) >= 5 && $m[4] !== '' ? substr($m[4], 1) : null,
			'buildMetadata' => count($m) >= 6 && $m[5] !== '' ? substr($m[5], 1) : null
		];
	}


	/**
	 * https://semver.org/#spec-item-11
	 *
	 * @param string $a https://semver.org/#summary
	 * @param string $b https://semver.org/#summary
	 *
	 * @return int 0 if equals, -1 if a less than b, 1 else
	 *
	 * @throws Exception for invalid $a || $b
	 */
	static public function compare(string $a, string $b): int {
		$sa = self::split($a);
		$sb = self::split($b);

		foreach(['major', 'minor', 'patch'] as $k) {
			$sa[$k] = intval($sa[$k]);
			$sb[$k] = intval($sb[$k]);
		}

		if ($sa['major'] != $sb['major']) {
			return $sa['major'] < $sb['major'] ? -1 : 1;
		}
		else if ($sa['minor'] != $sb['minor']) {
			return $sa['minor'] < $sb['minor'] ? -1 : 1;
		}
		else if ($sa['patch'] != $sb['patch']) {
			return $sa['patch'] < $sb['patch'] ? -1 : 1;
		}
		else if ($sa['preRelease'] && $sb['preRelease']) {
			$preA = explode('.', $sa['preRelease']);
			$preB = explode('.', $sb['preRelease']);

			for ($i=0; $i<count($preA); $i++) {
				// equal until now and a has more fields
				if ($i + 1 > count($preB)) {
					return 1;
				}

				$va = $preA[$i];
				$vb = $preB[$i];

				if (is_numeric($va) && is_numeric($vb)) {
					$va = intval($va);
					$vb = intval($vb);
					if ($va < $vb) {
						return -1;
					}
					else if ($va > $vb) {
						return 1;
					}
				}
				// "Numeric identifiers always have lower precedence than non-numeric identifiers."
				else if (is_numeric($va)) {
					return -1;
				}
				else if (is_numeric($vb)) {
					return 1;
				}
				// lexicographic comparison
				else if ($va < $vb) {
					return -1;
				}
				else if ($va > $vb) {
					return 1;
				}
			}

			if (count($preB) > count($preA)) {
				return -1;
			}
		}
		// pre-release has lower precedence
		else if ($sa['preRelease']) {
			return -1;
		}
		else if ($sb['preRelease']) {
			return 1;
		}

		return 0;
	}
}


