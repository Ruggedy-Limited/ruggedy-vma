<?php
​
namespace App\Transformers;
​
use App\Entities\PerformanceIndicator;
use League\Fractal\TransformerAbstract;

class PerformanceIndicatorTransformer extends TransformerAbstract
{
     /**
     * Transform a PerformanceIndicator entity for the API
     *
     * @param PerformanceIndicator $performance_indicator
     * @return array
     */
    public function transform(PerformanceIndicator $performanceIndicator)
    {
        return [
            'id'                   => $performance_indicator->getId(),
            'name'                 => $performance_indicator->getName(),
            'emailAddress'         => $performance_indicator->getEmail(),
            'photo'                => $performance_indicator->getPhotoUrl(),
            'twoFactorAuthEnabled' => $performance_indicator->getUsesTwoFactorAuth(),
            'createdDate'          => $performance_indicator->getCreatedAt(),
            'modifiedDate'         => $performance_indicator->getUpdatedAt(),
        ];
    }
}