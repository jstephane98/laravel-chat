<?php

namespace App\Http\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case FILE = 'file';

    /**
     * Return an array of all message type values.
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            self::TEXT->value,
            self::IMAGE->value,
            self::VIDEO->value,
            self::AUDIO->value,
            self::FILE->value,
        ];
    }

    /**
     * Return an associative array with the message type values as keys, and the corresponding type names as values.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return [
            self::TEXT->name => self::TEXT->value,
            self::IMAGE->name => self::IMAGE->value,
            self::VIDEO->name => self::VIDEO->value,
            self::AUDIO->name => self::AUDIO->value,
            self::FILE->name => self::FILE->value,
        ];
    }
}
