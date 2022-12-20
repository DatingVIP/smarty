<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */
/**
 * Smarty truncate modifier plugin
 * Type:     modifier
 * Name:     truncate
 * Purpose:  Truncate a string to a certain length if necessary,
 *               optionally splitting in the middle of a word, and
 *               appending the $etc string or inserting $etc into the middle.
 *
 * @link   https://www.smarty.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 *
 * @param string  $string      input string
 * @param integer $length      length of truncated text
 * @param string  $etc         end string
 * @param boolean $break_words truncate at word boundary
 * @param boolean $middle      truncate in the middle of text
 *
 * @return string truncated string
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
{
    if ($length === 0) {
        return '';
    }
    if (mb_strlen($string, \Smarty\Smarty::$_CHARSET) > $length) {
        $length -= min($length, mb_strlen($etc, \Smarty\Smarty::$_CHARSET));
        if (!$break_words && !$middle) {
            $string = preg_replace(
                '/\s+?(\S+)?$/' . \Smarty\Smarty::$_UTF8_MODIFIER,
                '',
                mb_substr($string, 0, $length + 1, \Smarty\Smarty::$_CHARSET)
            );
        }
        if (!$middle) {
            return mb_substr($string, 0, $length, \Smarty\Smarty::$_CHARSET) . $etc;
        }
        return mb_substr($string, 0, intval($length / 2), \Smarty\Smarty::$_CHARSET) . $etc .
               mb_substr($string, -intval($length / 2), $length, \Smarty\Smarty::$_CHARSET);
    }
    return $string;
}