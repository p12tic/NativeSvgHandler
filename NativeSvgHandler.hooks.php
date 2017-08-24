<?php

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

        global $wgNativeSvgHandlerEnableLinks;
        if(!isset($wgNativeSvgHandlerEnableLinks) || $wgNativeSvgHandlerEnableLinks) {
            return new ThumbnailImage($image, $image->getURL(), $params['width'],
                                      $params['height'], $image->getPath() );
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
        return Xml::element('img', $attribs);
    }
}