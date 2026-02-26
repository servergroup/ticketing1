<?php

namespace app\components;

use yii\log\FileTarget;
use yii\log\Logger;

class JsonFileTarget extends FileTarget
{
    public function formatMessage($message)
    {
        [$text, $level, $category, $timestamp] = $message;

        if (!is_string($text)) {
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = json_encode($text, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        $entry = [
            'timestamp' => $this->getTime($timestamp),
            'level' => Logger::getLevelName($level),
            'category' => $category,
            'prefix' => $this->getMessagePrefix($message),
            'message' => $text,
        ];

        if (isset($message[4]) && !empty($message[4])) {
            $entry['traces'] = $message[4];
        }

        return json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
