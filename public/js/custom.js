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

    var chatInterval = setInterval(function () {
        // Get the time since the newest chat and the vulnerability ID
        var timeSince       = $('ul.chat > li:first-child').find('.time-since'),
            newerThan       = timeSince.data('created-at') || '0000-00-00 00:00:00',
            vulnerabilityId = timeSince.data('vulnerability-id');

        // Make sure we have both otherwise we can't send the request
        if (!newerThan || !vulnerabilityId) {
            return;
        }

        $.ajax({
            url:      '/comments/updated/' + vulnerabilityId,
            type:     'POST',
            dataType: 'HTML',
            data:     'newer-than=' + newerThan
        }).done(function (comments) {
            // If there are no new comments, there's nothing to do
            if (!comments || comments.length < 1) {
                return;
            }

            // Prepend the new comments to the comments list and reset the comment count value
            $(comments).prependTo('ul.chat');
            $('#comment-count').text($('ul.chat > li').length);
            // TODO: update all the dates of all the other comments
        }).fail(function (jqXHR) {
            // Check which error was encountered
            var firstDigit = (''+jqXHR.status)[0],
                body       = $('body');

            if ((firstDigit != '5' && firstDigit != '4') || !body.data('comment-poll')) {
                return;
            }

            // If a 5xx for 4xx error was encountered then stop polling for comments until the next page request
            clearInterval(body.data('comment-poll'));
        });
    }, 20000);

    // Store the chat interval in a data attribute on the body element
    $('body').data('comment-poll', chatInterval);
})(jQuery);

// Enable existing comment editing
(function ($) {
    // Make sure the there are comment edit buttons on the page
    var commentEditBtns = $('.edit-comment');
    if (commentEditBtns.length < 1) {
        return;
    }

    // Bind a click handler to the edit comment button
    commentEditBtns.on('click', function (e) {
        // Make sure we can get the comment ID
        var commentId = $(this).data('comment-id');
        if (!commentId) {
            return;
        }

        // Prevent the default action and fade out the comment text and fade in the comment editor
        e.preventDefault();
        $('#comment-text-' + commentId).fadeOut(300, function() {
            $('#comment-editor-' + commentId).fadeIn(300);
        });
    });

    // Cancel comment editing button
    $('.edit-comment-cancel').on('click', function(e) {
        // Make sure we can get the comment ID
        var commentId = $(this).data('comment-id');
        if (!commentId) {
            return;
        }

        // Prevent the default action and fade out the comment editor fade in the comment text
        e.preventDefault();
        $('#comment-editor-' + commentId).fadeOut(300, function() {
            $('#comment-text-' + commentId).fadeIn(300);
        });
    });
})(jQuery);

// Enable existing comment edit form post when the "Save Changes" button is clicked
(function ($) {
    // Make sure we have a save comment button
    var saveCommentBtn = $('.btn-edit-chat');
    if (saveCommentBtn.length < 1) {
        return;
    }

    // When the save comment button is clicked, submit the form to save the comment
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
        // Hide the error alert after 5 seconds
        hideAlert    = function() {
            var alert = modalContent.find('.alert');
            if (alert.length <  1) {
                return;
            }

            setTimeout(function () {
                alert.slideUp(800);
                alert.remove();
            }, 5000);
        };

    // When the Send to Jira form is submitted
    jiraForm.on('submit', function (e) {
        // Prevent the default form submission
        e.preventDefault();

        // Send an ajax request with all the form data
        $.ajax({
            url: $(this).prop('action'),
            type: $(this).prop('method'),
            dataType: 'JSON',
            data: $(this).serialize(),
            // Overlay the form with a light-grey overlay and a loading icon
            beforeSend: function () {
                overlayAndIcon.fadeIn(300);
            }
        }).done(function(data) {
            // Successful ajax request, but invalid response. Show the default error.
            if (!data.html) {
                modalContent.prepend(defaultError);
                return;
            }

            // Prepend the form with the response message
            modalContent.prepend(data.html);
            if (data.isError) {
                // If the response is an error, hide it after 5s.
                hideAlert();
                return;
            }

            // Success: don't hide the success message because it contains the issue URL, but reset the form.
            jiraForm.find('input').val("");
        }).fail(function () {
            // Show the default error.
            modalContent.prepend(defaultError);
            hideAlert();
        }).always(function () {
            // Always hide the overlay and loading icon when the ajax request is done
            overlayAndIcon.fadeOut(300);
        });
    })
})(jQuery);