<?php

namespace App\Http\Controllers;

use App\Commands\CreateComment;
use App\Commands\DeleteComment;
use App\Commands\EditComment;
use App\Commands\GetNewComments;
use App\Entities\Comment;
use Exception;

/**
 * @Middleware({"web", "auth"})
 */
class CommentController extends AbstractController
{
    /**
     * Create a new comment
     *
     * @POST("/comment/create/{vulnerabilityId}", as="comment.create", where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function createComment($vulnerabilityId)
    {
        // Validate the request
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $comment = new Comment();
        $comment->setContent(clean($this->request->get('comment')));

        $command = new CreateComment(intval($vulnerabilityId), $comment);
        $comment = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comment)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("A new comment was posted successfully.");
        return redirect(route('vulnerability.view', ['vulnerabilityId' => $vulnerabilityId]) . '#tab5');
    }

    /**
     * Edit an existing comment
     *
     * @POST("/comment/edit/{commentId}", as="comment.edit", where={"commentId":"[0-9]+"})
     *
     * @param $commentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editComment($commentId)
    {
        // Validate the request
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $command = new EditComment(intval($commentId), [
            Comment::CONTENT => clean($this->request->get('comment-' . $commentId))
        ]);

        $comment = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comment)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("Comment updated successfully.");
        return redirect(redirect()->back()->getTargetUrl() . '#tab5');
    }

    /**
     * Delete an existing comment
     *
     * @GET("/comment/delete/{commentId}", as="comment.remove", where={"commentId":"[0-9]+"})
     *
     * @param $commentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteComment($commentId)
    {
        $command = new DeleteComment($commentId, true);
        $comment = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comment)) {
            return redirect()->back();
        }

        $this->flashMessenger->success("Comment deleted successfully.");
        return redirect(redirect()->back()->getTargetUrl() . '#tab5');
    }

    /**
     * Get new comments
     *
     * @POST("/comments/updated/{vulnerabilityId}", as="comments.get.updated", where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws Exception
     */
    public function getNewComments($vulnerabilityId)
    {
        $command = new GetNewComments(
            $vulnerabilityId,
            $this->request->get('newer-than', '0000-00-00 00:00:00')
        );

        $comments = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comments)) {
            throw new Exception($comments->getMessage());
        }

        return view('partials.comment', ['comments' => $comments]);
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [];
    }
}