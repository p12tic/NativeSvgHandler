<?php
/*
    Copyright 2012 Povilas Kanapickas <tir5c3@yahoo.co.uk>

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

if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of MediaWiki, it is not a valid entry point.\n";
	exit( 1 );
}

$wgMediaHandlers['image/svg+xml'] = 'NativeSvgHandler';
if ( !in_array( 'svg', $wgFileExtensions ) ) {
    $wgFileExtensions[] = 'svg';
}

$wgExtensionCredits['media'][] = array(
    'path'           => __FILE__,
    'name'           => 'NativeSvgHandler',
    'author'         => 'Povilas Kanapickas',
    'descriptionmsg' => 'nativesvghandler_desc',
    'url'            => 'https://github.com/p12tic/NativeSvgHandler',
    'version'        => '1.1',
);

$wgExtensionMessagesFiles['NativeSvgHandler'] = dirname( __FILE__ ) . '/' . 'NativeSvgHandler.i18n.php';

/**
 * Handler for SVG images that will be resized by the clients.
 *
 * @ingroup Media
 */
class NativeSvgHandler extends SvgHandler {

    function isEnabled() {
        return true;
    }

    function normaliseParams($image, &$params) {
        global $wgSVGMaxSize;
        if (!ImageHandler::normaliseParams($image, $params)) {
            return false;
        }
        return true;
    }

    function doTransform($image, $dstPath, $dstUrl, $params, $flags = 0) {
        if ( !$this->normaliseParams( $image, $params ) ) {
            return new TransformParameterError( $params );
        }

        return new SvgImage($image, $image->getURL(), $params['width'],
                            $params['height'], $image->getPath() );
    }

    function getThumbType($ext, $mime, $params = null) {
        return array( 'svg', 'image/svg+xml' );
    }
}

class SvgImage extends MediaTransformOutput {

    function __construct( $file, $url, $width, $height, $path = false, $page = false ) {
        $this->file = $file;
        $this->url = $url;

        $this->width = round( $width ); //paranoid
        $this->height = round( $height ); //paranoid

        $this->path = $path;
        $this->page = $page;
    }

    function toHtml( $options = array() ) {
        if ( count( func_get_args() ) == 2 ) {
            throw new MWException( __METHOD__ .' called in the old style' );
        }

        $alt = empty( $options['alt'] ) ? '' : $options['alt'];

        $query = empty( $options['desc-query'] ) ? '' : $options['desc-query'];

        if ( !empty( $options['custom-url-link'] ) ) {
            $linkAttribs = array( 'href' => $options['custom-url-link'] );
            if ( !empty( $options['title'] ) ) {
                $linkAttribs['title'] = $options['title'];
            }
            if ( !empty( $options['custom-target-link'] ) ) {
                $linkAttribs['target'] = $options['custom-target-link'];
            } elseif ( !empty( $options['parser-extlink-target'] ) ) {
                $linkAttribs['target'] = $options['parser-extlink-target'];
            }
            if ( !empty( $options['parser-extlink-rel'] ) ) {
                $linkAttribs['rel'] = $options['parser-extlink-rel'];
            }
        } elseif ( !empty( $options['custom-title-link'] ) ) {
            $title = $options['custom-title-link'];
            $linkAttribs = array(
                'href' => $title->getLinkURL(),
                'title' => empty( $options['title'] ) ? $title->getFullText() : $options['title']
            );
        } elseif ( !empty( $options['desc-link'] ) ) {
            $linkAttribs = $this->getDescLinkAttribs( empty( $options['title'] ) ? null : $options['title'], $query );
        } elseif ( !empty( $options['file-link'] ) ) {
            $linkAttribs = array( 'href' => $this->file->getURL() );
        } else {
            $linkAttribs = false;
        }

        $attribs = array(
            'alt' => $alt,
            'src' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
        );
        if ( !empty( $options['valign'] ) ) {
            $attribs['style'] = "vertical-align: {$options['valign']}";
        }
        if ( !empty( $options['img-class'] ) ) {
            $attribs['class'] = $options['img-class'];
        }

        return $this->linkWrap( $linkAttribs, Xml::element( 'img', $attribs ) );
    }
}
