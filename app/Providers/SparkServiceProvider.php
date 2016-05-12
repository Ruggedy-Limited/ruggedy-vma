<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Your Company',
        'product' => 'Your Product',
        'street' => 'PO Box 111',
        'location' => 'Your Town, NY 12345',
        'phone' => '555-555-5555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        //
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        /**
         * Free account = 50Mb
         * Restrict API-based apps
         * 1 User, 1 Project and 1 Workspace, 10 assets
         *
         * Plan 1 = 500Mb = NZD$40/month
         * All scanner apps
         * 5 Projects, 2 Workspaces per project, unlimited assets
         *
         * Plan 2 = 1Gb = NZD$80/month
         * All scanner apps
         * Unlimited Projects and Workspaces, unlimited assets
         */
        Spark::useStripe()->noCardUpFront();

        Spark::freePlan('Free Trial')
            ->maxCollaborators(1)
            ->features([
                '50Mb of Storage',
                'All file output Scanner Apps',
                '1 User Account',
                '1 Project',
                '1 Workspace',
                '10 Assets',
            ]);

        Spark::teamPlan('Basic Plan', 'stripe-id-basic-plan')
            ->price(40)
            ->maxTeamMembers(10)
            ->maxTeams(5)
            ->maxCollaborators(10)
            ->features([
                '500Mb of Storage',
                'Access to all Scanner Apps',
                '10 User Accounts',
                '5 Teams',
                '5 Projects',
                '2 Workspaces per Project',
                'Unlimited Assets<sup>*</sup>',
            ]);

        Spark::teamPlan('Pro Plan', 'stripe-id-pro-plan')
            ->price(80)
            ->maxTeamMembers(50)
            ->maxTeams(10)
            ->maxCollaborators(50)
            ->features([
                '1Gb of Storage',
                'Access to all Scanner Apps',
                '50 User Accounts',
                '10 Teams',
                'Unlimited Projects &amp; Workspaces',
                'Unlimited Assets<sup>*</sup>',
            ]);
    }
}
