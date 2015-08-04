<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

$autoloader = require $autoloaderClassPath;
$autoloader->addPsr4( 'SBL\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SBL\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );

print( "Semantic MediaWiki: " . SMW_VERSION . " ({$GLOBALS['smwgDefaultStore']}, {$GLOBALS['wgDBtype']})\n\n" );
