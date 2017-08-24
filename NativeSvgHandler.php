<?php
/*
    Copyright 2012 Povilas Kanapickas <povilas@radix.lt>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'NativeSvgHandler' );
	$wgMessageDirs['NativeSvgHandler'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for NativeSvgHandler extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
} else {
	die( 'This version of the NativeSvgHandler extension requires MediaWiki 1.29+' );
}

