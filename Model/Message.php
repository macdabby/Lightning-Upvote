<?php

namespace Modules\Upvote\Model;

use Lightning\Model\Object;

class Message extends Object {
    const TABLE = 'upvote_message';
    const PRIMARY_KEY = 'message_id';
}
