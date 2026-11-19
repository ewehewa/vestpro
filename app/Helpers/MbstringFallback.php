<?php

/*
 * Fallback for servers where the PHP mbstring extension is not enabled.
 *
 * symfony/polyfill-mbstring supplies most mb_* functions in that case, but not
 * mb_strimwidth(), which Laravel's Str::limit() relies on. This defines it only
 * when it is missing, so it does nothing on servers that have mbstring enabled.
 * The preferred fix is still to enable mbstring on the server.
 */

if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth(string $string, int $start, int $width, string $trim_marker = '', ?string $encoding = null): string
    {
        $encoding = $encoding ?? mb_internal_encoding();
        $string = mb_substr($string, $start, null, $encoding);

        if ($width < 0) {
            $width = max(0, mb_strwidth($string, $encoding) + $width);
        }

        if (mb_strwidth($string, $encoding) <= $width) {
            return $string;
        }

        $available = max(0, $width - mb_strwidth($trim_marker, $encoding));
        $result = '';
        $used = 0;

        foreach (preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $char) {
            $charWidth = mb_strwidth($char, $encoding);
            if ($used + $charWidth > $available) {
                break;
            }
            $result .= $char;
            $used += $charWidth;
        }

        return $result.$trim_marker;
    }
}
