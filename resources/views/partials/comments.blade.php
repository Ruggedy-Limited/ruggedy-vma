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
                    @foreach ($comments as $comment)
                        <li>
                            <div class="chat-body">
                                <div class="header">
                                    @if ($comment->isDeletable())
                                        <a class="pull-right" href="{{ route('comment.remove', [
                                            'commentId' => $comment->getId(),
                                        ]) }}">
                                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    @if ($comment->isEditable())
                                        <a class="pull-right edit-comment" href="{{ route('comment.edit', [
                                            'commentId' => $comment->getId(),
                                        ]) }}" data-comment-id="{{ $comment->getId() }}">
                                            <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    <strong class="primary-font">{{ $comment->getUser()->getName() }}</strong>
                                    <p class="text-muted">
                                        <small class=" text-muted">
                                            <span class="fa fa-clock-o"></span>
                                            <span class="time-since"
                                                  data-created-at="{{ $comment->getCreatedAt()->format("Y-m-d H:i:s") }}"
                                                  data-vulnerability-id="{{ $vulnerability->getId() }}">
                                                {{ $comment->getTimeSinceComment() }}
                                            </span>
                                        </small>
                                    </p>
                                </div>
                                <div id="comment-text-{{ $comment->getId() }}" class="chat-content">
                                    {!! $comment->getContent() !!}
                                </div>
                                @if ($comment->isEditable())
                                    <div id="comment-editor-{{ $comment->getId() }}" class="comment-editor">
                                        <div>
                                            <form class="comment-edit-form" action="{{ route('comment.edit', ['commentId' => $comment->getId()]) }}" method="POST">
                                                {{ csrf_field() }}
                                                <textarea class="comment-edit-txt post-form-control" name="comment-{{ $comment->getId() }}" rows="1" placeholder="Type your comment here...">
                                                    {!! $comment->getContent() !!}
                                                </textarea>
                                            </form>
                                        </div>
                                        <div>
                                            <button class="primary-btn btn-edit-chat" data-comment-id="{{ $comment->getId() }}">Save Changes</button>
                                            <button class="btn btn-cancel edit-comment-cancel" data-comment-id="{{ $comment->getId() }}">Cancel</button>
                                            <script type="text/javascript">
                                                CKEDITOR.replace( 'comment-{{ $comment->getId() }}', {
                                                    customConfig: '/js/ckeditor_config.js',
                                                    height: 100
                                                });
                                            </script>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>