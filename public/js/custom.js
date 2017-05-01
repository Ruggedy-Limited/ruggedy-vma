(function ($) {
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
})(jQuery);

// Always send CSRF token with ajax requests.
(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });
})(jQuery);

// Slide up alert messages after 4 seconds.
(function ($) {
    $(document).ready(function () {
        var firstAlert = $('.container:first > .alert');
        if (firstAlert.length !== 1) {
            return;
        }

        setTimeout(function () {
            firstAlert.slideUp(500);
        }, 4000);
    });
})(jQuery);

// Add the tab selector to the URL as a fragment when the tab changes and if a tab selector fragment is found in the URL
// when the page is loaded, select the relevant tab.
(function ($) {
    // Add the tab selector to the URL as a fragment when the tab changes
    $('input[name="tabs"][type="radio"]').on('change', function () {
        var newLocation = window.location.toString().replace(/#tab[0-9]+/g, '');
        window.location = newLocation + '#' + $(this).prop('id');
    });

    // If a tab selector fragment is found in the URL when the page is loaded, select the relevant tab.
    $(document).ready(function () {
        var matches = window.location.toString().match(/#tab[0-9]+/g);
        // Check the URL for tab selectors
        if (!matches || matches.length < 1) {
            return;
        }

        // Make sure the tab related to the selected exists in the DOM
        if ($(matches[0]).length < 1) {
            return;
        }

        // Select the relevant tab
        $(matches[0]).prop('checked', 'checked');
    });
})(jQuery);

// Click handler for the "Post" button to create new comments
(function ($) {
    $('#btn-chat').on('click', function (e) {
        e.preventDefault();
        $('#comment-form').submit();
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
    var submitAjaxForm = function (formContainer, defaultErrorTxt, done, hideAfterSuccess) {
        if (formContainer.length < 1) {
            return;
        }

        defaultErrorTxt = defaultErrorTxt
            || "Something went wrong and we could not complete your request this time. Please try again.";

        var form = formContainer.find('form'),
            modalContent = formContainer.find('.modal-content'),
            overlayAndIcon = formContainer.find('.waiting-icon-container, .waiting-overlay'),
            defaultError = '<div class="alert alert-danger">'
                + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                + defaultErrorTxt + '</div>',
            // Hide the error alert after 5 seconds
            hideAlert = function (form) {
                var alert = modalContent.find('.alert');
                if (alert.length < 1 || !form) {
                    return;
                }

                form.find('input:visible, select:visible').on('focusin', function () {
                    alert.slideUp(500, function () {
                        $(this).remove();
                    });
                });
            };

        // When the Send to Jira form is submitted
        form.on('submit', function (e) {
            // Prevent the default form submission
            e.preventDefault();
            var form = $(this);

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
            }).done(function (data) {
                // Successful ajax request, but invalid response. Show the default error.
                if (!data.message) {
                    modalContent.prepend(defaultError);
                    return;
                }

                // Prepend the form with the response message
                modalContent.prepend(data.message);
                if (data.isError) {
                    // If the response is an error, hide it after 5s.
                    hideAlert(form);
                    return;
                }

                // Success: hide the success message only if the hideAfterSuccess parameter is set
                if (hideAfterSuccess) {
                    hideAlert(form);
                }

                // Reset the form inputs
                form.find('input:not([type="submit"]):not([name="_token"]), select').val("");

                // Execute custom done functionality
                if (done) {
                    done(data);
                }
            }).fail(function () {
                // Show the default error.
                modalContent.prepend(defaultError);
                hideAlert(form);
            }).always(function () {
                // Always hide the overlay and loading icon when the ajax request is done
                overlayAndIcon.fadeOut(300);
            });
        });
    };

    // Jira issue creation
    submitAjaxForm(
        $('#jira'),
        'Something went wrong and we were not able to create an new Jira Issue for you, but please try again.'
    );

    // New asset creation for the Ruggedy app
    submitAjaxForm(
        $('#add-asset-form'),
        "Something went wrong and we were not able to create a new Asset for you, but please try again.",
        function(data) {
            var assetContainer = $('#related-assets'),
                assetsSelect = $('#assets-select');
            // Make sure everything we need exists in the DOM
            if (assetContainer.length < 1 || assetsSelect.length < 1 || !data.html) {
                return;
            }

            // Append the Asset HTML to right-hand side of the form as a visual aid
            $(data.html).appendTo(assetContainer);

            // Clear the select options and reset them to all the added Assets as selected options of the multiple
            // select that will be sent when the Vulnerability record is sent
            assetsSelect.html("");
            assetContainer.find('.asset').each(function () {
                $('<option />').val($(this)
                    .data('asset-id'))
                    .prop('selected', 'selected')
                    .appendTo(assetsSelect);
            });
        },
        true
    );

})(jQuery);

// Enable removal of thumbnail images from custom Vulnerabilities
(function ($) {
    $('.remove-poc').on('click', function (e) {
        e.preventDefault();
        var mustDelete = confirm("Are you sure you want to remove this image? This cannot be undone.");
        if (!mustDelete) {
            return;
        }

        var thumbnailBlock = $(this).parent('.remove-block').parent();
        thumbnailBlock.siblings('label, input').removeClass('hidden');
        thumbnailBlock.remove();
    });
})(jQuery);

// Enable removing of Assets in the Ruggedy App Vulnerability view
(function ($) {
    $('.remove-asset').on('click', function (e) {
        e.preventDefault();

        // Confirm the action to delete an Asset
        var mustDelete = confirm("Are you sure you want to remove this Asset? This cannot be undone.");
        if (!mustDelete) {
            return;
        }

        // Initialise variables
        var defaultError   = '<div class="alert alert-danger">'
            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
            + 'The Asset could not be deleted. Please try again.</div>',
            relatedAssets  = $('#related-assets'),
            iconAndOverlay = $('.container:first').children('.waiting-icon-container, .waiting-overlay'),
            assetBlock     = $(this).parent('div').parent('div').parent('a').parent('.asset'),
            assetId        = $(this).data('asset-id'),
            assetsSelect   = $('#assets-select');

        // Prepare and send the ajax request
        $.ajax({
            url: '/asset/delete/' + assetId,
            type: 'GET',
            dataType: 'JSON',
            // Overlay the window with a light-grey overlay and a loading icon
            beforeSend: function () {
                iconAndOverlay.fadeIn(300)
            }
        }).fail(function () {
            // Show a standard error message
            relatedAssets.prepend(defaultError);
        }).done(function (data) {
            // Successful ajax request, but invalid response. Show the default error.
            if (!data.message) {
                relatedAssets.prepend(defaultError);
                return;
            }

            // Prepend the assets div with the response message
            relatedAssets.prepend(data.message);
            if (data.isError) {
                return;
            }

            // After 3.5 seconds slide up and remove the related Asset block and
            // remove the asset ID from the assets[] multiple select input
            setTimeout(function () {
                assetBlock.slideUp(500, function () {
                    $(this).remove();
                    // Clear the select options and reset them to all the added Assets as selected options of the
                    // multiple select that will be sent when the Vulnerability record is sent
                    assetsSelect.html("");
                    relatedAssets.find('.asset').each(function () {
                        $('<option />').val($(this)
                            .data('asset-id'))
                            .prop('selected', 'selected')
                            .appendTo(assetsSelect);
                    });
                });
            }, 3500);
        }).always(function () {
            // Clear the loading overlay and icon
            iconAndOverlay.fadeOut(300);

            // Find the alert message
            var alert = relatedAssets.find('.alert:first');
            if (alert.length !== 1) {
                return;
            }

            // Slide the alert message up after 3 seconds
            setTimeout(function () {
                alert.slideUp(500, function () {
                    $(this).remove();
                });
            }, 3000);
        });
    });
})(jQuery);