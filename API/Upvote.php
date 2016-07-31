<?php

namespace Modules\Upvote\API;

use Exception;
use Lightning\Tools\ClientUser;
use Lightning\Tools\Database;
use Lightning\Tools\Form;
use Lightning\Tools\PHP;
use Lightning\View\API;
use Modules\Upvote\Model\Channel;
use Modules\Upvote\Model\Message;
use Overridable\Lightning\Tools\Request;

class Upvote extends API {

    /**
     * Get replies to a message.
     */
    public function getReplies() {
        // Load 10 replies. And 3 replies under each of those.
        $message = Request::get('message', Request::TYPE_INT);

        $loaded = Request::get('ignore', Request::TYPE_ARRAY, Request::TYPE_INT);

        $replies = Database::getInstance()->selectAllQuery([
            'select' => ['message_id', 'user_id', 'upvotes', 'reply_count' => 'replies', 'time', 'message'],
            'from' => 'upvote_message',
            'where' => [
                'parent_id' => $message,
                'message_id' => ['NOT IN', $loaded],
            ],
            'order_by' => ['score' => 'DESC'],
            'limit' => 11,
        ]);

        $output = [
            'more' => false,
            'replies' => $replies,
        ];

        if (count($replies) > 10) {
            $output['more'] = true;
            unset($output['replies'][10]);
        }

        foreach ($output['replies'] as &$reply) {
            $replies = Database::getInstance()->selectAllQuery([
                'select' => ['message_id', 'user_id', 'upvotes', 'reply_count' => 'replies', 'time', 'message'],
                'from' => 'upvote_message',
                'where' => [
                    'parent_id' => $reply['message_id'],
                ],
                'order_by' => ['score' => 'DESC'],
                'limit' => 4,
            ]);
            if (count($replies) > 3) {
                $reply['more'] = true;
                unset($replies[3]);
            }
            $reply['replies'] = $replies;
        }

        return $output;
    }

    /**
     * Create a new thread in a channel.
     */
    public function postMessage() {
        // Security.
        ClientUser::requireLogin();

        Form::validateToken();

        $parent_id = Request::post('parent_id', Request::TYPE_INT);
        $parent_message = Message::loadByID($parent_id);
        if (empty($parent_message)) {
            $channel_id = Request::get('locator', Request::TYPE_STRING);
            $channel = Channel::loadByLocator($channel_id);
            if (empty($channel)) {
                throw new Exception('Invalid Thread');
            }
        }

        $message = Request::post('message', Request::TYPE_STRING);
        if (empty($message) || strlen($message) > 255) {
            throw new Exception('Invalid Message');
        }

        if (!empty($parent_message)) {
            $new_message = new Message([
                'channel_id' => $parent_message->channel_id,
                'parent_id' => $parent_message->id,
                'message' => $message,
                'user_id' => ClientUser::getInstance()->id,
                'time' => time(),
            ]);
            $new_message->save();

            // Increment the number of replies in the parent thread.
            $parent_message->incrementReplies();
        } elseif (!empty($channel)) {
            $new_message = new Message([
                'channel_id' => $channel->id,
                'message' => $message,
                'user_id' => ClientUser::getInstance()->id,
                'time' => time(),
            ]);
            $new_message->save();
        }

        return [
            'message_id' => $new_message->id,
        ];
    }

    public function postVote() {
        // Security.
        ClientUser::requireLogin();

        Form::validateToken();

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

        return [
            'new_upvote_count' => Database::getInstance()->selectField('upvotes', 'upvote_message', ['message_id' => $message_id])
        ];
    }
}
