<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\JiraIssue
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`jira_issues`", indexes={@ORM\Index(name="jira_issues_file_fk_idx", columns={"`file_id`"}), @ORM\Index(name="jira_issues_vulnerability_fk_idx", columns={"`vulnerability_id`"})})
 */
class JiraIssue extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'jira_issues';

    /** Column name constants */
    const PROJECT_KEY      = 'project_key';
    const ISSUE_ID         = 'issue_id';
    const ISSUE_KEY        = 'issue_key';
    const ISSUE_STATUS     = 'issue_status';
    const ISSUE_TYPE       = 'issue_type';
    const SUMMARY          = 'summary';
    const DESCRIPTION      = 'description';
    const REQUEST_TYPE     = 'request_type';
    const REQUEST_STATUS   = 'request_status';
    const HOST             = 'host';
    const PORT             = 'port';
    const RETRIES          = 'retries';
    const FAILURE_REASON   = 'failure_reason';
    const FILE_ID          = 'file_id';
    const VULNERABILITY_ID = 'vulnerability_id';
    const FILE             = 'file';
    const VULNERABILITY    = 'vulnerability';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`project_key`", type="string", length=255)
     */
    protected $project_key;

    /**
     * @ORM\Column(name="`issue_id`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $issue_id;

    /**
     * @ORM\Column(name="`issue_key`", type="string", length=255, nullable=true)
     */
    protected $issue_key;

    /**
     * @ORM\Column(name="`issue_status`", type="string", nullable=true)
     */
    protected $issue_status;

    /**
     * @ORM\Column(name="`issue_type`", type="string", length=100, nullable=true)
     */
    protected $issue_type;

    /**
     * @ORM\Column(name="`summary`", type="string", length=255, nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(name="`description`", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="`request_type`", type="string")
     */
    protected $request_type;

    /**
     * @ORM\Column(name="`request_status`", type="string")
     */
    protected $request_status;

    /**
     * @ORM\Column(name="`host`", type="string", length=255, nullable=true)
     */
    protected $host;

    /**
     * @ORM\Column(name="`port`", type="integer", nullable=true)
     */
    protected $port;

    /**
     * @ORM\Column(name="`retries`", type="boolean")
     */
    protected $retries;

    /**
     * @ORM\Column(name="`failure_reason`", type="string", length=255, nullable=true)
     */
    protected $failure_reason;

    /**
     * @ORM\Column(name="`file_id`", type="integer", options={"unsigned":true})
     */
    protected $file_id;

    /**
     * @ORM\Column(name="`vulnerability_id`", type="integer", options={"unsigned":true})
     */
    protected $vulnerability_id;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="jiraIssues", cascade={"persist"})
     * @ORM\JoinColumn(name="`file_id`", referencedColumnName="`id`", nullable=false, onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="Vulnerability", inversedBy="jiraIssues", cascade={"persist"})
     * @ORM\JoinColumn(name="`vulnerability_id`", referencedColumnName="`id`", nullable=false, onDelete="CASCADE")
     */
    protected $vulnerability;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\JiraIssue
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of project_key.
     *
     * @param string $project_key
     * @return \App\Entities\Base\JiraIssue
     */
    public function setProjectKey($project_key)
    {
        $this->project_key = $project_key;

        return $this;
    }

    /**
     * Get the value of project_key.
     *
     * @return string
     */
    public function getProjectKey()
    {
        return $this->project_key;
    }

    /**
     * Set the value of issue_id.
     *
     * @param integer $issue_id
     * @return \App\Entities\Base\JiraIssue
     */
    public function setIssueId($issue_id)
    {
        $this->issue_id = $issue_id;

        return $this;
    }

    /**
     * Get the value of issue_id.
     *
     * @return integer
     */
    public function getIssueId()
    {
        return $this->issue_id;
    }

    /**
     * Set the value of issue_key.
     *
     * @param string $issue_key
     * @return \App\Entities\Base\JiraIssue
     */
    public function setIssueKey($issue_key)
    {
        $this->issue_key = $issue_key;

        return $this;
    }

    /**
     * Get the value of issue_key.
     *
     * @return string
     */
    public function getIssueKey()
    {
        return $this->issue_key;
    }

    /**
     * Set the value of issue_status.
     *
     * @param string $issue_status
     * @return \App\Entities\Base\JiraIssue
     */
    public function setIssueStatus($issue_status)
    {
        $this->issue_status = $issue_status;

        return $this;
    }

    /**
     * Get the value of issue_status.
     *
     * @return string
     */
    public function getIssueStatus()
    {
        return $this->issue_status;
    }

    /**
     * Set the value of issue_type.
     *
     * @param string $issue_type
     * @return \App\Entities\Base\JiraIssue
     */
    public function setIssueType($issue_type)
    {
        $this->issue_type = $issue_type;

        return $this;
    }

    /**
     * Get the value of issue_type.
     *
     * @return string
     */
    public function getIssueType()
    {
        return $this->issue_type;
    }

    /**
     * Set the value of summary.
     *
     * @param string $summary
     * @return \App\Entities\Base\JiraIssue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get the value of summary.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \App\Entities\Base\JiraIssue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of request_type.
     *
     * @param string $request_type
     * @return \App\Entities\Base\JiraIssue
     */
    public function setRequestType($request_type)
    {
        $this->request_type = $request_type;

        return $this;
    }

    /**
     * Get the value of request_type.
     *
     * @return string
     */
    public function getRequestType()
    {
        return $this->request_type;
    }

    /**
     * Set the value of request_status.
     *
     * @param string $request_status
     * @return \App\Entities\Base\JiraIssue
     */
    public function setRequestStatus($request_status)
    {
        $this->request_status = $request_status;

        return $this;
    }

    /**
     * Get the value of request_status.
     *
     * @return string
     */
    public function getRequestStatus()
    {
        return $this->request_status;
    }

    /**
     * Set the value of host.
     *
     * @param string $host
     * @return \App\Entities\Base\JiraIssue
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the value of host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the value of port.
     *
     * @param integer $port
     * @return \App\Entities\Base\JiraIssue
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the value of port.
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the value of retries.
     *
     * @param boolean $retries
     * @return \App\Entities\Base\JiraIssue
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * Get the value of retries.
     *
     * @return boolean
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * Set the value of failure_reason.
     *
     * @param string $failure_reason
     * @return \App\Entities\Base\JiraIssue
     */
    public function setFailureReason($failure_reason)
    {
        $this->failure_reason = $failure_reason;

        return $this;
    }

    /**
     * Get the value of failure_reason.
     *
     * @return string
     */
    public function getFailureReason()
    {
        return $this->failure_reason;
    }

    /**
     * Set the value of file_id.
     *
     * @param integer $file_id
     * @return \App\Entities\Base\JiraIssue
     */
    public function setFileId($file_id)
    {
        $this->file_id = $file_id;

        return $this;
    }

    /**
     * Get the value of file_id.
     *
     * @return integer
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * Set the value of vulnerability_id.
     *
     * @param integer $vulnerability_id
     * @return \App\Entities\Base\JiraIssue
     */
    public function setVulnerabilityId($vulnerability_id)
    {
        $this->vulnerability_id = $vulnerability_id;

        return $this;
    }

    /**
     * Get the value of vulnerability_id.
     *
     * @return integer
     */
    public function getVulnerabilityId()
    {
        return $this->vulnerability_id;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\JiraIssue
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of updated_at.
     *
     * @param \DateTime $updated_at
     * @return \App\Entities\Base\JiraIssue
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of updated_at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set File entity (many to one).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\JiraIssue
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get File entity (many to one).
     *
     * @return \App\Entities\Base\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set Vulnerability entity (many to one).
     *
     * @param \App\Entities\Base\Vulnerability $vulnerability
     * @return \App\Entities\Base\JiraIssue
     */
    public function setVulnerability(Vulnerability $vulnerability = null)
    {
        $this->vulnerability = $vulnerability;

        return $this;
    }

    /**
     * Get Vulnerability entity (many to one).
     *
     * @return \App\Entities\Base\Vulnerability
     */
    public function getVulnerability()
    {
        return $this->vulnerability;
    }

    public function __sleep()
    {
        return array('id', 'project_key', 'issue_id', 'issue_key', 'issue_status', 'issue_type', 'summary', 'description', 'request_type', 'request_status', 'host', 'port', 'retries', 'failure_reason', 'file_id', 'vulnerability_id', 'created_at', 'updated_at');
    }
}