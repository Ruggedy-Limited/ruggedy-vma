<?php

namespace App\Commands;

class CreateAsset extends CreateSomething
{
    /** @var bool */
    protected $multiMode;

    /**
     * CreateAsset constructor.
     *
     * @param int $id
     * @param array $details
     * @param bool $multiMode
     */
    public function __construct($id, array $details, bool $multiMode = false)
    {
        parent::__construct($id, $details);
        $this->multiMode = $multiMode;
    }

    /**
     * @return boolean
     */
    public function isMultiMode()
    {
        return $this->multiMode;
    }
}