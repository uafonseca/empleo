<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 23/06/20
 * Time: 23:25
 */

namespace App\Traits;


trait UuidEntityTrait
{
    /**
     * @ORM\Column(type="guid", nullable=true)
     */
    private $uuid;

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     *
     * @return self
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}