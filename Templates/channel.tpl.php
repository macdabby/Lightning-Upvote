<?php
use Lightning\Tools\ClientUser;
use Lightning\Tools\Scrub;
?>
<div class="upvote" data-locator="<?= Scrub::toHTML($upvote->locator); ?>">
    <?php if (!ClientUser::getInstance()->isAnonymous()): ?>
        <div>
            <textarea></textarea>
            <span class="button submit" data-parent="0">Submit</span>
        </div>
    <?php endif; ?>
    <div class="hide">
        <div class="reply-template">
            <textarea></textarea>
            <span class="button submit" data-parent="0">Submit</span>
        </div>
        <div class="message-template">
            <div class="rank">
                <i class="fa fa-arrow-up"></i>
                <span class="rank-sum">##</span>
                <i class="fa fa-arrow-down"></i>
            </div>
            <div class="content">
                <div class="author"></div>
                <div class="body"></div>
                <div class="replies"></div>
            </div>
        </div>
    </div>
</div>
