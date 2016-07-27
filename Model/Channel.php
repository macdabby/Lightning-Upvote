<?php

namespace Modules\Upvote\Model;

use Lightning\Model\Object;
use Lightning\Tools\Database;

class Channel extends Object {

    const TABLE = 'upvote_channel';
    const PRIMARY_KEY = 'channel_id';

    public static function loadByLocator($locator) {
        if ($row = Database::getInstance()->selectRow(static::TABLE, [
            'locator' => $locator
        ])) {
            return new static($row);
        } else {
            return null;
        }
    }

    public static function loadOrCreateByLocator($locator, $name = '') {
        if ($channel = static::loadByLocator($locator)) {
            return $channel;
        } else {
            $db = Database::getInstance();
            $db->insert(static::TABLE, ['locator' => $locator, 'name' => $name]);
            return static::loadByLocator($locator);
        }
    }
}
