# SemverPHP

Semantic Versioning 2.0.0 utility library:
 - Comparison
 - Validation
 - Split

## Installing / Running

    composer require brunorb/semverphp
    use BrunoRB\SemverPHP;

## API

### isValid(_semver_) -> bool

	$v1 = '1.2.0-alpha+001';
	$v2 = '1.2.1';
    SemverPHP::isValid(v1); // true
    SemverPHP::isValid(v2); // true
    SemverPHP::isValid('1.1'); // false

### split(_semver_) -> array

	$v1 = '1.2.0-alpha+001';
	$v2 = '1.2.1+001';

	SemverPHP::split(v1); // [major: '1', minor: '2', patch: '0', preRelease: 'alpha', 'buildMetadata': '001']
	SemverPHP::split(v2); // [major: '1', minor: '2', patch: '1', preRelease: null, buildMetadata: '001']

### compare(_semver1_, _semver2_) -> int

	$v1 = '1.2.0-alpha+001';
	$v2 = '1.2.1';

	SemverPHP::compare(v1, v2); // -1
	SemverPHP::compare(v2, v1); // 1
	SemverPHP::compare(v1, v1); // 0

### PATTERN -> string

	preg_match(SemverPHP::PATTERN, 'semver');
	preg_replace(SemverPHP::PATTERN, 'semver');
	... other regex methods


## Tests

[tests/BasicTest.php](tests/BasicTest.php)

`composer run-script test`

## License

[The MIT License](LICENSE)