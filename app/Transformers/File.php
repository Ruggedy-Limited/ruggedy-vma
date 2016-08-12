<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;
​
class FileTransformer extends TransformerAbstract
{
     /**
     * Transform a File entity for the API
     *
     * @param File $file
     * @return array
     */
    public function transform(File $file)
    {
        return [
            'id'                   => $file->getId(),
            'name'                 => $file->getName(),
            'emailAddress'         => $file->getEmail(),
            'photo'                => $file->getPhotoUrl(),
            'twoFactorAuthEnabled' => $file->getUsesTwoFactorAuth(),
            'createdDate'          => $file->getCreatedAt(),
            'modifiedDate'         => $file->getUpdatedAt(),
        ]
    }
}