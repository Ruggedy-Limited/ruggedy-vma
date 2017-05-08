@foreach ($comments as $comment)
    <li>
        <div class="chat-body">
            <div class="header">
                @if ($comment->isDeletable())
                    <a class="pull-right btn round-btn c-red" href="{{ route('comment.remove', [
                                                'commentId' => $comment->getId(),
                                            ]) }}">
                        <i class="fa fa-trash fa-lg" aria-hidden="true"></i>
                    </a>
                @endif
                @if ($comment->isEditable())
                    <a class="pull-right edit-comment btn round-btn c-purple" href="{{ route('comment.edit', [
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
                              data-vulnerability-id="{{ $comment->getVulnerability()->getId() }}">
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