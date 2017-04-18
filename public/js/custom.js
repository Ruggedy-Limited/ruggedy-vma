$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

// Click handler for the "Post" button to create new comments
(function ($) {
    $('#btn-chat').on('click', function (e) {
        e.preventDefault();
        $('#comment-form').submit();

        /*var clickTime = Math.floor(Date.now() / 1000);
        ckEditor.updateElement();
        $.ajax({
            url:      commentForm.prop('action'),
            type:     commentForm.prop('method'),
            dataType: 'json',
            data:     'comment=' + $('#comment-txt'),
            beforeSend: function () {
                ckEditor.showNotification('Posting...', 'info').show();
            }
        }).then(
            function (comment) {
                console.log(comment);
                if (comment.error) {
                    ckEditor.showNotification('There was a problem posting your comment.').show();
                    return;
                }

                var newComment = $('ul.chat > li:first-child').clone();
                newComment.find('strong.primary-font').html(comment.user.name);

                var timeAgo = Math.floor(Date.now() / 1000) - clickTime;
                if (timeAgo === 0) {
                    timeAgo = 1;
                }
                newComment.find('.time-since').html(timeAgo + ' seconds ago');
                newComment.find('.chat-body > p').html(comment.content);

                newComment.prependTo('ul.chat');
            },
            function () {
                ckEditor.showNotification('There was a problem posting your comment.').show();
            }
        );*/
    });
})(jQuery);

// Check for new comments every 30 seconds
(function ($) {
    if ($('.chat-card').length < 1) {
        return;
    }

    var chatTimeout = setInterval(function () {
        var timeSince       = $('ul.chat > li:first-child').find('.time-since'),
            newerThan       = timeSince.data('created-at') || '0000-00-00 00:00:00',
            vulnerabilityId = timeSince.data('vulnerability-id');

        if (!newerThan || !vulnerabilityId) {
            return;
        }

        $.ajax({
            url:      '/comments/updated/' + vulnerabilityId,
            type:     'POST',
            dataType: 'HTML',
            data:     'newer-than=' + newerThan
        }).done(function (comments) {
            if (!comments || comments.length < 1) {
                return;
            }

            $(comments).prependTo('ul.chat');
            $('#comment-count').text($('ul.chat > li').length);
        }).fail(function (jqXHR) {
            var firstDigit = (''+jqXHR.status)[0],
                body       = $('body');

            if ((firstDigit != '5' && firstDigit != '4') || !body.data('comment-poll')) {
                return;
            }

            clearInterval(body.data('comment-poll'));
        });
    }, 20000);

    $('body').data('comment-poll', chatTimeout);
})(jQuery);

// Enable existing comment editing
(function ($) {
    var commentEditBtns = $('.edit-comment');
    if (commentEditBtns.length < 1) {
        return;
    }

    commentEditBtns.on('click', function (e) {
        var commentId = $(this).data('comment-id');
        if (!commentId) {
            return;
        }

        e.preventDefault();
        $('#comment-text-' + commentId).fadeOut(300, function() {
            $('#comment-editor-' + commentId).fadeIn(300);
        });
    });

    $('.edit-comment-cancel').on('click', function(e) {
        var commentId = $(this).data('comment-id');
        if (!commentId) {
            return;
        }

        e.preventDefault();
        $('#comment-editor-' + commentId).fadeOut(300, function() {
            $('#comment-text-' + commentId).fadeIn(300);
        });
    });
})(jQuery);

// Enable existing comment edit form post when the "Save Changes" button is clicked
(function ($) {
    var saveCommentBtn = $('.btn-edit-chat');
    if (saveCommentBtn.length < 1) {
        return;
    }

    saveCommentBtn.on('click', function (e) {
        var commentId = $(this).data('comment-id');
        if (!commentId) {
            return;
        }

        e.preventDefault();
        $('#comment-editor-' + commentId).find('form').submit();
    });
})(jQuery);

// Send to Jira with Ajax
(function ($) {
    var jiraContainer = $('#jira');
    if (jiraContainer.length < 1) {
        return;
    }

    var jiraForm       = jiraContainer.find('form'),
        modalContent   = jiraContainer.find('.modal-content'),
        overlayAndIcon = jiraContainer.find('.waiting-icon-container, .waiting-overlay'),
        defaultError   = '<div class="alert alert-danger">'
        + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
        + 'Jira Issue creation failed this time. Please try again.</div>',
        hideAlert    = function() {
            var alert = modalContent.find('.alert');
            if (alert.length <  1) {
                return;
            }

            setTimeout(function () {
                alert.slideUp(300);
                alert.remove();
            }, 3000);
        };

    jiraForm.on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).prop('action'),
            type: $(this).prop('method'),
            dataType: 'JSON',
            data: $(this).serialize(),
            beforeSend: function () {
                overlayAndIcon.fadeIn(300);
            }
        }).done(function(data) {
            if (!data.html) {
                modalContent.prepend(defaultError);
                return;
            }

            modalContent.prepend(data.html);
            if (data.isError) {
                hideAlert();
                return;
            }

            jiraForm.find('input').val("");
        }).fail(function () {
            modalContent.prepend(defaultError);
            hideAlert();
        }).always(function () {
            overlayAndIcon.fadeOut(300);
        });
    })
})(jQuery);