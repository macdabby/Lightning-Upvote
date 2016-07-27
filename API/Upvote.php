<?php

namespace Modules\Upvote\API;

use Exception;
use Lightning\Tools\ClientUser;
use Lightning\Tools\Database;
use Lightning\View\API;
use Modules\Upvote\Model\Message;
use Overridable\Lightning\Tools\Request;

class Upvote extends API {

    /**
     * Get replies to a message.
     */
    public function getReplies() {
        $message = Request::get('message', Request::TYPE_INT);
    }

    public function postMessage() {

    }

    public function postReply() {

    }

    public function postVote() {
        // Security.
//        ClientUser::requireLogin();

        $direction = Request::post('vote', Request::TYPE_INT);
        if (abs($direction) !== 1) {
            throw new Exception('Invalid Vote');
        }
        $message_id = Request::post('message', Request::TYPE_INT);

        $data = [
            'message_id' => $message_id,
            'user_id' => 1//ClientUser::getInstance()->id,
        ];
        $vote = ['vote' => $direction];
        $result = Database::getInstance()->insert('upvote_user_vote', $data + $vote, $vote, true);
        $adjustment = 0;
        switch ($result) {
            case 0:
                // The user had already voted this way.
                break;
            case 1:
                // This is a new vote, so add it to the message total.
                $adjustment = $direction;
                break;
            case 2:
                // The vote was changed, so add the inverse.
                $adjustment = 2 * $direction;
                break;
        }

        if (!empty($adjustment)) {
            Database::getInstance()->update('upvote_message', [
                'upvotes' => [
                    'expression' => 'upvotes + ?',
                    'vars' => [$adjustment],
                ]
            ], ['message_id' => $message_id]);
        }
    }
}
