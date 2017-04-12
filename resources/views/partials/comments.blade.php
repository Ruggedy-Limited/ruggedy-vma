<div class="col-md-12">
    <div>
        <form id="comment-form" action="{{ route('comment.create', ['vulnerabilityId' => $vulnerability->getId()]) }}" method="POST">
            {{ csrf_field() }}
            <textarea id="comment-txt" class="post-form-control" name="comment" rows="1" placeholder="Type your comment here..."></textarea>
        </form>
    </div>
    <div>
        <button class="primary-btn" id="btn-chat">Post</button>
        <script type="text/javascript">
            var ckEditor = CKEDITOR.replace( 'comment', {
                customConfig: '/js/ckeditor_config.js',
                height: 100
            });
        </script>
    </div>
    <div class="chat-card">
        <div>
            @if (empty($comments))
                <p>No comments yet.</p>
            @else
                <ul class="chat">
                    @include('partials.comment')
                </ul>
            @endif
        </div>
    </div>
</div>