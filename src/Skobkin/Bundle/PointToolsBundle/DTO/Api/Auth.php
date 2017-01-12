<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api;

use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class Auth
{
    /**
     * @var string
     *
     * @JMSS\SerializedName("token")
     * @JMSS\Type("string")
     */
    private $token;

    /**
     * @var string
     *
     * @JMSS\SerializedName("csrf_token")
     * @JMSS\Type("string")
     */
    private $csRfToken;

    /**
     * @var string
     *
     * @JMSS\SerializedName("error")
     * @JMSS\Type("string")
     */
    private $error;


    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCsRfToken(): ?string
    {
        return $this->csRfToken;
    }

    public function setCsRfToken(?string $csRfToken): self
    {
        $this->csRfToken = $csRfToken;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }
}