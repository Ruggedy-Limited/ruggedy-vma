<?php

namespace App\Models;

class PortModel
{
    /** @var int */
    protected $portNumber;

    /** @var string */
    protected $protocolName;

    /** @var string */
    protected $serviceName;

    /** @var string */
    protected $extraInformation;

    /**
     * @return int
     */
    public function getPortNumber()
    {
        return $this->portNumber;
    }

    /**
     * @param int $portNumber
     */
    public function setPortNumber($portNumber)
    {
        $this->portNumber = $portNumber;
    }

    /**
     * @return string
     */
    public function getProtocolName()
    {
        return $this->protocolName;
    }

    /**
     * @param string $protocolName
     */
    public function setProtocolName($protocolName)
    {
        $this->protocolName = $protocolName;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @return string
     */
    public function getExtraInformation()
    {
        return $this->extraInformation;
    }

    /**
     * @param string $extraInformation
     */
    public function setExtraInformation($extraInformation)
    {
        $this->extraInformation = $extraInformation;
    }
}