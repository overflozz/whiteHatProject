<?php

namespace OF\ContractsBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contract
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="contracts")
 * @ORM\Entity(repositoryClass="OF\ContractsBundle\Repository\ContractRepository")
 */
class Contract
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="bounty", type="integer")
     */
    private $bounty;

    /**
     * @ORM\Column(type="text", nullable=true)

    */
    private $difficulty;

    /**
     * @ORM\Column(type="text", nullable=true)

    */
    private $address;
    /**
     * @ORM\Column(type="text", nullable=true)

    */
    private $name;
    /**
     * @ORM\Column(type="text", nullable=true)

    */
    private $report;

    /**
     * @ORM\Column(type="text", nullable=true)

    */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="OF\ContractsBundle\Entity\Company", inversedBy="contracts")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    
    private $company;

    /**
     * @ORM\Column(type="boolean", nullable=true)

    */
    private $disabled;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set bounty
     *
     * @param integer $bounty
     *
     * @return Contract
     */
    public function setBounty($bounty)
    {
        $this->bounty = $bounty;

        return $this;
    }

    /**
     * Get bounty
     *
     * @return int
     */
    public function getBounty()
    {
        return $this->bounty;
    }

    /**
     * Set difficulty
     *
     * @param string $difficulty
     *
     * @return Contract
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get difficulty
     *
     * @return string
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set report
     *
     * @param string $report
     *
     * @return Contract
     */
    public function setReport($report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return string
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Contract
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set company
     *
     * @param \OF\ContractsBundle\Entity\Company $company
     *
     * @return Contract
     */
    public function setCompany(\OF\ContractsBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \OF\ContractsBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Contract
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Contract
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     *
     * @return Contract
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }
}
