<?php

namespace App\Transformers;

use App\Entities\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'file',
        'vulnerability',
    ];

    /**
     * Transform an Comment entity
     *
     * @param Comment $comment
     * @return array
     */
    public function transform(Comment $comment)
    {
        return [
            'id'                  => $comment->getId(),
            'content'             => $comment->getContent(),
            'owner'               => $comment->getUser(),
            'createdDate'         => $comment->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'        => $comment->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for the related File
     *
     * @param Comment $comment
     * @return \League\Fractal\Resource\Item
     */
    public function includeFile(Comment $comment)
    {
        return $this->item($comment->getFile(), new FileTransformer());
    }

    /**
     * Optional include for the related Vulnerability
     *
     * @param Comment $comment
     * @return \League\Fractal\Resource\Item
     */
    public function includeVulnerability(Comment $comment)
    {
        return $this->item($comment->getVulnerability(), new VulnerabilityTransformer());
    }
}