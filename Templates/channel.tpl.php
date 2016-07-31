<?php
use Lightning\Tools\ClientUser;
use Lightning\Tools\Scrub;
?>
<style>
    .upvote .replies, .upvote .more {
        /*padding-left: 15px;*/
    }
    .upvote .rank {
        float:left;
        margin-right: 10px;
        text-align: center;
    }
    .upvote .rank i, .upvote .rank .span {
        display: block;
    }
    .upvote .message {
        overflow: hidden;
    }
    .upvote .content {
        overflow: hidden;
        float: left;
    }
</style>
<div class="upvote" data-locator="<?= Scrub::toHTML($upvote->locator); ?>">
    <div class="message" data-message_id="0">
        <div class="content">
            <div class="reply">
                <textarea></textarea>
                <span class="button submit small">Submit</span>
            </div>
            <div class="replies">
            </div>
            <div class="more">More</div>
        </div>
    </div>
    <div class="hide">
        <div class="message-template">
            <div class="rank">
                <i class="fa fa-arrow-up vote-up"></i>
                <span class="rank-sum">##</span>
                <i class="fa fa-arrow-down vote-down"></i>
            </div>
            <div class="content">
                <div class="author"></div>
                <div class="body"></div>
                <div class="options">
                    <span class="reply-show">Reply</span>
                </div>
                <div class="reply hide">
                </div>
                <div class="replies">
                </div>
                <div class="more">More</div>
            </div>
        </div>
        <div class="reply-template">
            <textarea></textarea>
            <span class="button submit small">Submit</span>
        </div>
    </div>
</div>
