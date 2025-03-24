<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ProcessStatusEnum;
use App\Repository\LaunchProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LaunchProcessRepository::class)]
class LaunchProcess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(
        type: 'string',
        length: 20,
        enumType: ProcessStatusEnum::class,
    )]
    private ?ProcessStatusEnum $status = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?ProcessStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ProcessStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }
}
