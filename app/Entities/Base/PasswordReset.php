<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\PasswordReset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\PasswordResetRepository")
 * @ORM\Table(name="`password_resets`", indexes={@ORM\Index(name="password_resets_email_index", columns={"`email`"}), @ORM\Index(name="password_resets_token_index", columns={"`token`"})})
 */
class PasswordReset extends AbstractEntity
{
    /**
     * @ORM\Column(name="`email`", type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(name="`token`", type="string", length=255)
     */
    protected $token;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    public function __construct()
    {
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return \App\Entities\Base\PasswordReset
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of token.
     *
     * @param string $token
     * @return \App\Entities\Base\PasswordReset
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\PasswordReset
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

    public function __sleep()
    {
        return array('email', 'token', 'created_at');
    }
}