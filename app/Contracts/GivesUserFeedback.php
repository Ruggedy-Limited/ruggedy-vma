<?php

namespace App\Contracts;


interface GivesUserFeedback
{
    /**
     * Get the namespace for the translator to find the relevant response message
     *
     * @return string
     */
    function getTranslatorNamespace(): string;
}