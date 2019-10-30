<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bundles
 *
 * @ORM\Entity(repositoryClass="App\Repository\BundlesRepository")
 * @ORM\Table(name="bundles")
 * @ORM\Entity
 */
class Bundles
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bundle_product_id", type="string", length=45, nullable=true)
     */
    private $bundleProductId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="simple_product_id", type="string", length=45, nullable=true)
     */
    private $simpleProductId;



    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getBundleProductId()
    {
        return $this->bundleProductId;
    }

    /**
     * @return string|null
     */
    public function getSimpleProductId()
    {
        return $this->simpleProductId;
    }

    public function setBundleProductId(?string $bundleProductId): self
    {
        $this->bundleProductId = $bundleProductId;

        return $this;
    }

    public function setSimpleProductId(?string $simpleProductId): self
    {
        $this->simpleProductId = $simpleProductId;

        return $this;
    }
}
