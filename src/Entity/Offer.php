<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\Table(
    indexes: [
        new ORM\Index(
            name: 'offer_name_idx',
            columns: ['name'],
        ),
    ],
)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['offer.private'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['offer.private'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['offer.private'])]
    private ?int $approvalTime = null;

    #[ORM\Column(length: 255)]
    #[Groups(['offer.private'])]
    private ?string $siteUrl = null;

    #[ORM\Column(length: 255)]
    #[Groups(['offer.private'])]
    private ?string $logo = null;

    #[ORM\Column]
    #[Groups(['offer.private'])]
    private ?float $rating = null;

    /**
     * @var Collection<int, Geo>
     */
    #[ORM\ManyToMany(
        targetEntity: Geo::class,
        inversedBy: 'offers',
        cascade: ['persist'],
    )]
    private Collection $geo;

    #[ORM\Column(length: 5)]
    #[Groups(['offer.private'])]
    private ?string $offerCurrencyName = null;

    public function __construct()
    {
        $this->geo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getApprovalTime(): ?int
    {
        return $this->approvalTime;
    }

    public function setApprovalTime(int $approvalTime): static
    {
        $this->approvalTime = $approvalTime;

        return $this;
    }

    public function getSiteUrl(): ?string
    {
        return $this->siteUrl;
    }

    public function setSiteUrl(string $siteUrl): static
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Geo>
     */
    public function getGeo(): Collection
    {
        return $this->geo;
    }

    public function addGeo(Geo $geo): static
    {
        if (!$this->geo->contains($geo)) {
            $this->geo->add($geo);
        }

        return $this;
    }

    public function removeGeo(Geo $geo): static
    {
        $this->geo->removeElement($geo);

        return $this;
    }

    public function getOfferCurrencyName(): ?string
    {
        return $this->offerCurrencyName;
    }

    public function setOfferCurrencyName(string $offerCurrencyName): static
    {
        $this->offerCurrencyName = $offerCurrencyName;

        return $this;
    }
}
