(function(){
    var self;
    lightning.modules.upvote = {
        init: function(){
            $('.upvote').on('click', '.submit', self.submitMessage);
        },

        submitMessage: function(e){
            var target = $(e.target);
            var parent_id = target.data('parent');
            var locator = target.closest('.upvote').data('locator');
            var textarea = taregt.siblings('textarea');
            $.ajax({
                url: '/api/upvote',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    message: textarea.val(),
                    locator: locator,
                    parent_id: parent_id,
                },
                success: function(data){
                    // Prepend the new message.
                    // Clear the text box.
                    textarea.val('');
                },
            });
        },

        expandReplies: function() {

        },

        expandReply: function() {

        },

        voteUp: function() {
            self.vote(message, 1);
        },

        voteDown: function() {
            self.vote(message, -1);
        },

        vote: function(message, direction) {

        }
    };
    self = lightning.modules.upvote;
})();
