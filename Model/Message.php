<?php

namespace Modules\Upvote\Model;

use Lightning\Model\Object;
use Lightning\Tools\Database;

class Message extends Object {
    const TABLE = 'upvote_message';
    const PRIMARY_KEY = 'message_id';

    public function incrementReplies() {
        Database::getInstance()->update('upvote_message', [
            'replies' => [
                'expression' => 'replies + ?',
                'vars' => [1],
            ]
        ], ['message_id' => $this->id]);

        if ($this->parent_id > 0) {
            $parent = Message::loadByID($this->parent_id);
            $parent->incrementReplies();
        }
    }
}
