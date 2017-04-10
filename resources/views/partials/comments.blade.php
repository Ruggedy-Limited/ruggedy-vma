<div class="col-md-12">
    <div>
        <textarea class="post-form-control" name="comment" rows="1" placeholder="Type your comment here..."></textarea>
        <script>
         CKEDITOR.replace( 'comment', {
                customConfig: '/js/ckeditor_config.js',
                height: 100
            });
        </script>
    </div>
    <div>
        <button class="primary-btn" id="btn-chat">Post</button>
    </div>
    <div class="chat-card">
        <div>
            @if (empty($comments))
                <p>No comments yet.</p>
            @else
                <ul class="chat">
                    @foreach ($comments as $comment)
                        <li>
                            <div class="chat-body">
                                <div class="header">
                                    <strong class="primary-font">{{ $comment->getUser()->getName() }}</strong>
                                    <p class="text-muted">
                                        <small class=" text-muted">
                                            <span class="fa fa-clock-o"></span>
                                            {{ $comment->getTimeSinceComment() }}
                                        </small>
                                    </p>
                                </div>
                                <p>{{ $comment->getContent() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>