<?php

namespace Ngekoding\PdfMerger;

/**
 * CompressionLevel provides predefined constants for Ghostscript PDF compression settings.
 *
 * Ghostscript offers several presets through the `-dPDFSETTINGS` option to control
 * the output quality and file size. Each setting balances quality and compression differently.
 *
 * Reference: https://ghostscript.readthedocs.io/en/latest/VectorDevices.html#controls-and-features-specific-to-postscript-and-pdf-input
 */
class CompressionLevel
{
    const NONE      = '';
    const SCREEN    = '/screen';
    const EBOOK     = '/ebook';
    const PRINTER   = '/printer';
    const PREPRESS  = '/prepress';
    const DEFAULT   = '/default';

    /**
     * Get all supported compression levels.
     *
     * @return array
     */
    public static function all()
    {
        return [
            self::NONE,
            self::SCREEN,
            self::EBOOK,
            self::PRINTER,
            self::PREPRESS,
            self::DEFAULT,
        ];
    }
}
