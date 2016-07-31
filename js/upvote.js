(function(){
    var self;
    lightning.modules.upvote = {
        init: function(){
            $('.upvote')
                .on('click', '.submit', self.submitMessage)
                .on('click', '.vote-down,.vote-up', self.vote)
                .on('click', '.more', self.expandReplies)
                .on('click', '.reply-show', self.showReplyForm)
                .each(function(){
                    var locator = $(this).data('locator');
                    self.expandReplies(0, locator);
                });
            self.messageTemplate = $('.message-template');
            self.replyFormTemplate = $('.reply-template');
        },

        submitMessage: function(e){
            var target = $(e.target);
            var message_container = target.closest('.message');
            var parent_id = message_container.data('message_id');
            var locator = target.closest('.upvote').data('locator');
            var textarea = target.siblings('textarea');
            $.ajax({
                url: '/api/upvote',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'message',
                    message: textarea.val(),
                    locator: locator,
                    parent_id: parent_id,
                    token: lightning.vars.token,
                },
                success: function(data){
                    // Prepend the new message.
                    var content = self.messageTemplate.clone();
                    content.removeClass('message-template').addClass('message');
                    content.attr('data-message_id', data.message_id);
                    content.find('.rank-sum').html(0);
                    content.find('.body').html(textarea.val());
                    content.find('.more').hide();
                    message_container.find('.replies').first().prepend(content);
                    // Clear the text box.
                    target.closest('.reply').hide().empty();
                },
            });
        },

        showReplyForm: function(e) {
            var template = self.replyFormTemplate.clone().removeClass('reply-template');
            $(e.target).parent().siblings('.reply').removeClass('hide').empty().append(template);
        },

        // Load replies
        expandReplies: function(e, locator) {
            var parent_message;
            var loaded = [];
            if (typeof e == 'object') {
                var target = $(e.target);
                parent_message = target.closest('.message').data('message_id');
                locator = target.closest('.upvote').data('locator');
                target.siblings().filter('.replies').children().each(function(){
                    loaded.push($(this).data('message_id'));
                });
            } else {
                parent_message = e;
            }
            $.ajax({
                url: '/api/upvote',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    action: 'replies',
                    message: parent_message,
                    locator: locator,
                    ignore: loaded,
                },
                success: function(data) {
                    console.log(data);
                    self.addReplies(locator, parent_message, data.replies, data.more);
                }
            });
        },

        addReplies: function(locator, message_id, replies, more) {
            var reply_container = $('.upvote[data-locator="' + locator + '"] [data-message_id="' + message_id + '"] .replies').first();
            for (var i in replies) {
                var content = self.messageTemplate.clone();
                content.removeClass('message-template').addClass('message');
                content.attr('data-message_id', replies[i].message_id);
                content.find('.rank-sum').html(lightning.format.count(replies[i].upvotes));
                content.find('.body').html(replies[i].message);
                var included_replies = replies[i].hasOwnProperty('replies') ? replies[i].replies.length : 0;
                if (replies[i].reply_count == included_replies) {
                    content.find('.more').hide();
                }
                reply_container.append(content);
                if (replies[i].replies) {
                    self.addReplies(locator, replies[i].message_id, replies[i].replies, replies[i].more);
                }
            }

            if (!more) {
                reply_container.siblings().filter('.more').hide();
            }
        },

        // Show the reply box to create a new reply
        expandReply: function() {

        },

        vote: function(e) {
            var target = $(e.target);
            var message = target.closest('.message').data('message_id');
            var direction = target.is('.vote-up') ? 1 : -1;
            var container = target.closest('.upvote')
            $.ajax({
                url: '/api/upvote',
                method: 'POST',
                data: {
                    action: 'vote',
                    vote: direction,
                    message: message,
                    token: lightning.vars.token,
                },
                success: function(data){
                    container.find('.message[data-message_id="' + message + '"] .rank-sum').html(lightning.format.count(data.new_upvote_count));
                }
            });
        }
    };
    self = lightning.modules.upvote;
})();
