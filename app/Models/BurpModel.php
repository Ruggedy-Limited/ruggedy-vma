<?php

namespace App\Models;

use App\Contracts\CollectsScanOutput;
use App\Entities\VulnerabilityReferenceCode;
use Illuminate\Support\Collection;

class BurpModel extends AbstractXmlModel implements CollectsScanOutput
{
    /** @var string */
    protected $vulnerabilityName;

    /** @var string */
    protected $severity;

    /** @var string */
    protected $issueBackground;

    /** @var string */
    protected $issueDetail;

    /** @var string */
    protected $remediationBackground;

    /** @var string */
    protected $remediationDetail;

    /** @var string */
    protected $interactionType;

    /** @var string */
    protected $originIp;

    /** @var string */
    protected $time;

    /** @var string */
    protected $lookupType;

    /** @var string */
    protected $lookupHost;

    /** @var string */
    protected $httpMethod;

    /** @var string */
    protected $httpUri;

    /** @var string */
    protected $httpRequest;

    /** @var string */
    protected $httpResponse;

    /** @var string */
    protected $onlineReferences;

    /** @var Collection */
    protected $exportForVulnerabilityMap;

    /**
     * BurpModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Map the vulnerability data to the Vulnerability entity properties
        $this->exportForVulnerabilityMap = new Collection([
            'name'           => 'getVulnerabilityName',
            'severity'       => 'getSeverity',
            'description'    => 'getDescription',
            'solution'       => 'getSolution',
            'generic_output' => 'getGenericOutput',
            'http_method'    => 'getHttpMethod',
            'http_uri'       => 'getHttpUri',
            'http_request'   => 'getHttpRequest',
            'httpResponse'   => 'getHttpResponse',
        ]);

        // Map the vulnerability reference data to the VulnerabilityReferenceCode entity properties
        $this->exportForVulnerabilityRefsMap = new Collection([
            'reference_type' => 'getVulnerabilityReferenceType',
            'value'          => 'getOnlineReferences',
        ]);
    }

    /**
     * @return string
     */
    public function getVulnerabilityName()
    {
        return $this->vulnerabilityName;
    }

    /**
     * @param string $vulnerabilityName
     */
    public function setVulnerabilityName(string $vulnerabilityName)
    {
        $this->vulnerabilityName = $vulnerabilityName;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity(string $severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return string
     */
    public function getIssueBackground()
    {
        return $this->issueBackground;
    }

    /**
     * @param string $issueBackground
     */
    public function setIssueBackground(string $issueBackground)
    {
        $this->issueBackground = $issueBackground;
    }

    /**
     * @return string
     */
    public function getIssueDetail()
    {
        return $this->issueDetail;
    }

    /**
     * @param string $issueDetail
     */
    public function setIssueDetail(string $issueDetail)
    {
        $this->issueDetail = $issueDetail;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->issueBackground) && empty($this->issueDetail)) {
            return null;
        }

        $description = '';
        if (!empty($this->issueBackground)) {
            $description .= '<h3>Background</h3>' . PHP_EOL
                . $this->issueBackground . PHP_EOL;
        }

        if (!empty($this->issueDetail)) {
            $description .= '<h3>Detail</h3>' . PHP_EOL
                . $this->issueDetail;
        }

        return $description;
    }

    /**
     * @return string
     */
    public function getRemediationBackground()
    {
        return $this->remediationBackground;
    }

    /**
     * @param string $remediationBackground
     */
    public function setRemediationBackground(string $remediationBackground)
    {
        $this->remediationBackground = $remediationBackground;
    }

    /**
     * @return string
     */
    public function getRemediationDetail()
    {
        return $this->remediationDetail;
    }

    /**
     * @param string $remediationDetail
     */
    public function setRemediationDetail(string $remediationDetail)
    {
        $this->remediationDetail = $remediationDetail;
    }

    /**
     * @return string
     */
    public function getSolution()
    {
        if (empty($this->remediationBackground) && empty($this->remediationDetail)) {
            return null;
        }

        $solution = '';
        if (!empty($this->remediationBackground)) {
            $solution .= $this->remediationBackground . PHP_EOL;
        }

        if (!empty($this->remediationDetail)) {
            $solution .= $this->remediationDetail . PHP_EOL;
        }

        return $solution;
    }

    /**
     * @return string
     */
    public function getInteractionType()
    {
        return $this->interactionType;
    }

    /**
     * @param string $interactionType
     */
    public function setInteractionType(string $interactionType)
    {
        $this->interactionType = $interactionType;
    }

    /**
     * @return string
     */
    public function getOriginIp()
    {
        return $this->originIp;
    }

    /**
     * @param string $originIp
     */
    public function setOriginIp(string $originIp)
    {
        $this->originIp = $originIp;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime(string $time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getLookupType()
    {
        return $this->lookupType;
    }

    /**
     * @param string $lookupType
     */
    public function setLookupType(string $lookupType)
    {
        $this->lookupType = $lookupType;
    }

    /**
     * @return string
     */
    public function getLookupHost()
    {
        return $this->lookupHost;
    }

    /**
     * @param string $lookupHost
     */
    public function setLookupHost(string $lookupHost)
    {
        $this->lookupHost = $lookupHost;
    }

    /**
     * @return string
     */
    public function getGenericOutput()
    {
        $genericOutput = '';
        $genericOutput .= isset($this->interactionType) ? "Interaction type: {$this->interactionType}" . PHP_EOL : '';
        $genericOutput .= isset($this->originIp)        ? "Origin IP: {$this->originIp}" . PHP_EOL               : '';
        $genericOutput .= isset($this->time)            ? "Time: {$this->time}" . PHP_EOL                        : '';
        $genericOutput .= isset($this->lookupType)      ? "Lookup type: {$this->lookupType}" . PHP_EOL           : '';
        $genericOutput .= isset($this->lookupHost)      ? "Lookup host: {$this->lookupHost}"                     : '';

        return $genericOutput;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param string $httpMethod
     */
    public function setHttpMethod(string $httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return string
     */
    public function getHttpUri()
    {
        return $this->httpUri;
    }

    /**
     * @param string $httpUri
     */
    public function setHttpUri(string $httpUri)
    {
        $this->httpUri = $httpUri;
    }

    /**
     * @return string
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @param string $httpRequest
     */
    public function setHttpRequest(string $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * @return string
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @param string $httpResponse
     */
    public function setHttpResponse(string $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }

    /**
     * @return string
     */
    public function getVulnerabilityReferenceType()
    {
        if (empty($this->onlineReferences)) {
            return null;
        }

        return VulnerabilityReferenceCode::REF_TYPE_ONLINE_OTHER;
    }

    /**
     * @return string
     */
    public function getOnlineReferences()
    {
        return $this->onlineReferences;
    }

    /**
     * @param string $onlineReferences
     */
    public function setOnlineReferences(string $onlineReferences)
    {
        $this->onlineReferences = $onlineReferences;
    }
}