<?php

if ( PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL | E_STRICT );
date_default_timezone_set( 'UTC' );
ini_set( 'display_errors', 1 );

if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The Semantic MediaWiki test autoloader is not available' );
}

if ( !is_readable( $extensionJson = __DIR__ . '/../extension.json' ) ) {
	die( "\nSemantic Breadcrumb Links is not available, please check your Composer or LocalSettings.\n" );
}

$extensionInfo = json_decode( file_get_contents( $extensionJson ), true );

print sprintf( "\n%-27s%s\n", "Semantic Breadcrumb Links: ", $extensionInfo['version'] ?? 'UNKNOWN' );

$autoloader = require $autoloaderClassPath;
$autoloader->addPsr4( 'SBL\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SBL\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
unset( $autoloader );
