<?php

namespace App\Transformers;

use App\Entities\PerformanceIndicator;
use League\Fractal\TransformerAbstract;

class PerformanceIndicatorTransformer extends TransformerAbstract
{
     /**
     * Transform a PerformanceIndicator entity for the API
     *
     * @param PerformanceIndicator $performanceIndicator
     * @return array
     */
    public function transform(PerformanceIndicator $performanceIndicator)
    {
        return [
            'id'                      => $performanceIndicator->getId(),
            'monthlyRecurringRevenue' => $performanceIndicator->getMonthlyRecurringRevenue(),
            'yearlyRecurringRevenue'  => $performanceIndicator->getYearlyRecurringRevenue(),
            'dailyVolume'             => $performanceIndicator->getDailyVolume(),
            'newUsers'                => $performanceIndicator->getNewUsers(),
            'createdDate'             => $performanceIndicator->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'            => $performanceIndicator->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}