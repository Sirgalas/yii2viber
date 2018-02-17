<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 14.02.2018
 * Time: 15:37
 */

namespace common\components;
use yii\log\FileTarget;
class ViberLog extends FileTarget
{
    public function formatMessage($message)
    {
        echo "HERERERERRREERRERE";
        list($text, $level, $category, $timestamp) = $message;

        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = " " . basename($trace['file']) . ":{$trace['line']}";
            }
        }

        return "[" . date('d-m H:i:s', $timestamp) . "]      $text"
            . (empty($traces) ? '' : "\n                      [" . trim(implode(" ", $traces))) ."]";
    }
}