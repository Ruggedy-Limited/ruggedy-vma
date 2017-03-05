<?php

namespace Tests\Mocks;

use Illuminate\Http\UploadedFile;

class MockUploadedFile extends UploadedFile
{
    /**
     * @inheritdoc
     */
    public function isValid()
    {
        return true;
    }

    public function move($directory, $name = null)
    {
        return true;
    }
}