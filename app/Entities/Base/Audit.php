<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Audit
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`audits`")
 */
class Audit extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'audits';

    /** Column name constants */
    const AUDIT_FILE            = 'audit_file';
    const COMPLIANCE_CHECK_NAME = 'compliance_check_name';
    const COMPLIANCE_CHECK_ID   = 'compliance_check_id';
    const ACTUAL_VALUE          = 'actual_value';
    const POLICY_VALUE          = 'policy_value';
    const INFO                  = 'info';
    const RESULT                = 'result';
    const REFERENCE             = 'reference';
    const SEE_ALSO              = 'see_also';
    const DESCRIPTION           = 'description';
    const SOLUTION              = 'solution';
    const AGENT                 = 'agent';
    const UNAME                 = 'uname';
    const OUTPUT                = 'output';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`audit_file`", type="string", length=150, nullable=true)
     */
    protected $audit_file;

    /**
     * @ORM\Column(name="`compliance_check_name`", type="text", nullable=true)
     */
    protected $compliance_check_name;

    /**
     * @ORM\Column(name="`compliance_check_id`", type="string", length=45, nullable=true)
     */
    protected $compliance_check_id;

    /**
     * @ORM\Column(name="`actual_value`", type="text", nullable=true)
     */
    protected $actual_value;

    /**
     * @ORM\Column(name="`policy_value`", type="string", length=45, nullable=true)
     */
    protected $policy_value;

    /**
     * @ORM\Column(name="`info`", type="text", nullable=true)
     */
    protected $info;

    /**
     * @ORM\Column(name="`result`", type="string", nullable=true)
     */
    protected $result;

    /**
     * @ORM\Column(name="`reference`", type="string", length=255, nullable=true)
     */
    protected $reference;

    /**
     * @ORM\Column(name="`see_also`", type="string", length=255, nullable=true)
     */
    protected $see_also;

    /**
     * @ORM\Column(name="`description`", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="`solution`", type="text", nullable=true)
     */
    protected $solution;

    /**
     * @ORM\Column(name="`agent`", type="string", length=45, nullable=true)
     */
    protected $agent;

    /**
     * @ORM\Column(name="`uname`", type="string", length=255, nullable=true)
     */
    protected $uname;

    /**
     * @ORM\Column(name="`output`", type="text", nullable=true)
     */
    protected $output;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Audit
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
     * Set the value of audit_file.
     *
     * @param string $audit_file
     * @return \App\Entities\Base\Audit
     */
    public function setAuditFile($audit_file)
    {
        $this->audit_file = $audit_file;

        return $this;
    }

    /**
     * Get the value of audit_file.
     *
     * @return string
     */
    public function getAuditFile()
    {
        return $this->audit_file;
    }

    /**
     * Set the value of compliance_check_name.
     *
     * @param string $compliance_check_name
     * @return \App\Entities\Base\Audit
     */
    public function setComplianceCheckName($compliance_check_name)
    {
        $this->compliance_check_name = $compliance_check_name;

        return $this;
    }

    /**
     * Get the value of compliance_check_name.
     *
     * @return string
     */
    public function getComplianceCheckName()
    {
        return $this->compliance_check_name;
    }

    /**
     * Set the value of compliance_check_id.
     *
     * @param string $compliance_check_id
     * @return \App\Entities\Base\Audit
     */
    public function setComplianceCheckId($compliance_check_id)
    {
        $this->compliance_check_id = $compliance_check_id;

        return $this;
    }

    /**
     * Get the value of compliance_check_id.
     *
     * @return string
     */
    public function getComplianceCheckId()
    {
        return $this->compliance_check_id;
    }

    /**
     * Set the value of actual_value.
     *
     * @param string $actual_value
     * @return \App\Entities\Base\Audit
     */
    public function setActualValue($actual_value)
    {
        $this->actual_value = $actual_value;

        return $this;
    }

    /**
     * Get the value of actual_value.
     *
     * @return string
     */
    public function getActualValue()
    {
        return $this->actual_value;
    }

    /**
     * Set the value of policy_value.
     *
     * @param string $policy_value
     * @return \App\Entities\Base\Audit
     */
    public function setPolicyValue($policy_value)
    {
        $this->policy_value = $policy_value;

        return $this;
    }

    /**
     * Get the value of policy_value.
     *
     * @return string
     */
    public function getPolicyValue()
    {
        return $this->policy_value;
    }

    /**
     * Set the value of info.
     *
     * @param string $info
     * @return \App\Entities\Base\Audit
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get the value of info.
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set the value of result.
     *
     * @param string $result
     * @return \App\Entities\Base\Audit
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get the value of result.
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set the value of reference.
     *
     * @param string $reference
     * @return \App\Entities\Base\Audit
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get the value of reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set the value of see_also.
     *
     * @param string $see_also
     * @return \App\Entities\Base\Audit
     */
    public function setSeeAlso($see_also)
    {
        $this->see_also = $see_also;

        return $this;
    }

    /**
     * Get the value of see_also.
     *
     * @return string
     */
    public function getSeeAlso()
    {
        return $this->see_also;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \App\Entities\Base\Audit
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
     * Set the value of solution.
     *
     * @param string $solution
     * @return \App\Entities\Base\Audit
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;

        return $this;
    }

    /**
     * Get the value of solution.
     *
     * @return string
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * Set the value of agent.
     *
     * @param string $agent
     * @return \App\Entities\Base\Audit
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get the value of agent.
     *
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set the value of uname.
     *
     * @param string $uname
     * @return \App\Entities\Base\Audit
     */
    public function setUname($uname)
    {
        $this->uname = $uname;

        return $this;
    }

    /**
     * Get the value of uname.
     *
     * @return string
     */
    public function getUname()
    {
        return $this->uname;
    }

    /**
     * Set the value of output.
     *
     * @param string $output
     * @return \App\Entities\Base\Audit
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get the value of output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Audit
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
     * @return \App\Entities\Base\Audit
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

    public function __sleep()
    {
        return array('id', 'audit_file', 'compliance_check_name', 'compliance_check_id', 'actual_value', 'policy_value', 'info', 'result', 'reference', 'see_also', 'description', 'solution', 'agent', 'uname', 'output', 'created_at', 'updated_at');
    }
}