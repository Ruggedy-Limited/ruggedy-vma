<?php

namespace App\Http\Controllers\Api;

use App\Commands\CreateComment;
use App\Commands\DeleteComment;
use App\Commands\EditComment;
use App\Commands\GetComment;
use App\Commands\GetComments;
use App\Entities\Comment;
use App\Services\EntityFactoryService;
use App\Transformers\CommentTransformer;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class CommentController extends AbstractController
{
    /**
     * Fetch a single, existing comment
     *
     * @GET("/comment/{commentId}", as="comment.get", where={"commentId":"[0-9]+"})
     *
     * @param $commentId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function getComment($commentId)
    {
        $command = new GetComment($commentId);
        return $this->sendCommandToBusHelper($command, new CommentTransformer());
    }

    /**
     * Fetch a collection of comments related to a specific Vulnerability in a specific File
     *
     * @GET("/comments/{fileId}/{vulnerabilityId}", as="comments.get", where={"fileId":"[0-9]+",
     *     "vulnerabilityId":"[0-9]+"})
     *
     * @param $fileId
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function getComments($fileId, $vulnerabilityId)
    {
        $command = new GetComments($fileId, $vulnerabilityId);
        return $this->sendCommandToBusHelper($command, new CommentTransformer());
    }

    /**
     * Create a new comment on a Vulnerability that is found in a specific File
     *
     * @POST("/comment/create/{fileId}/{vulnerabilityId}", as="comment.create", where={"fileId":"[0-9]+",
     *     "vulnerabilityId":"[0-9]+"})
     *
     * @param $fileId
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function createComment($fileId, $vulnerabilityId)
    {
        $comment = EntityFactoryService::makeEntity(Comment::class, $this->request->json()->all());
        $command = new CreateComment($fileId, $comment, $vulnerabilityId);
        return $this->sendCommandToBusHelper($command, new CommentTransformer());
    }

    /**
     * Edit an existing comment
     *
     * @PUT("/comment/{commentId}", as="comment.edit", where={"commentId":"[0-9]+"})
     *
     * @param $commentId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editComment($commentId)
    {
        $command = new EditComment($commentId, $this->request->json()->all());
        return $this->sendCommandToBusHelper($command, new CommentTransformer());
    }

    /**
     * Delete an existing comment
     *
     * @DELETE("/comment/{commentId}", as="comment.delete", where={"commentId":"[0-9]+"})
     *
     * @param $commentId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function deleteComment($commentId)
    {
        $command = new DeleteComment($commentId, true);
        return $this->sendCommandToBusHelper($command, new CommentTransformer());
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
}