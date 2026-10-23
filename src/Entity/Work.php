<?php

namespace App\Entity;

use App\Repository\WorkRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\PriceMention;
use App\Enum\WorkType;

#[ORM\Entity(repositoryClass: WorkRepository::class)]
class Work
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Maximum {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 65, nullable: true)]
    #[Assert\Length(
        max: 65,
        maxMessage: 'Maximum {{ limit }} caractères.'
    )]
    private ?string $seoTitle = null;

    #[ORM\Column(type: 'string', length: 65, nullable: true)]
    #[Assert\Length(
        max: 65,
        maxMessage: 'Maximum {{ limit }} caractères.'
    )]
    private ?string $slugTitle = null;

    #[ORM\Column(length: 160, nullable: true)]
    #[Assert\Length(
        max: 160,
        maxMessage: 'Maximum {{ limit }} caractères.'
    )]
    private ?string $seoDescription = null;
    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Maximum {{ limit }} caractères.'
    )]
    private ?string $descriptionShort = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionLong = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $active = false;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['slugTitle'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    
    #[ORM\Column(type: 'string', enumType: WorkType::class, nullable: true)]
    private ?WorkType $type = null;

    #[ORM\Column]
    private ?bool $front = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $demoUrl = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $showStartingFrom = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $showHostingNote = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Maximum {{ limit }} caractères.'
    )]
    private ?string $priceNote = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function isFront(): ?bool
    {
        return $this->front;
    }

    public function setFront(bool $front): self
    {
        $this->front = $front;

        return $this;
    }

    public function getDemoUrl(): ?string
    {
        return $this->demoUrl;
    }

    public function setDemoUrl(?string $demoUrl): static
    {
        $this->demoUrl = $demoUrl;

        return $this;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): static
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    public function getSlugTitle(): ?string
    {
        return $this->slugTitle;
    }

    public function setSlugTitle(?string $slugTitle): static
    {
        $this->slugTitle = $slugTitle;

        return $this;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(?string $seoDescription): static
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    public function getType(): ?WorkType
    {
        return $this->type;
    }

    public function setType(?WorkType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescriptionShort(): ?string
    {
        return $this->descriptionShort;
    }

    public function setDescriptionShort(?string $descriptionShort): static
    {
        $this->descriptionShort = $descriptionShort;

        return $this;
    }

    public function getDescriptionLong(): ?string
    {
        return $this->descriptionLong;
    }

    public function setDescriptionLong(?string $descriptionLong): static
    {
        $this->descriptionLong = $descriptionLong;

        return $this;
    }

    public function isShowStartingFrom(): bool
    {
        return $this->showStartingFrom;
    }

    public function setShowStartingFrom(bool $showStartingFrom): static
    {
        $this->showStartingFrom = $showStartingFrom;

        return $this;
    }

    public function isShowHostingNote(): bool
    {
        return $this->showHostingNote;
    }

    public function setShowHostingNote(bool $showHostingNote): static
    {
        $this->showHostingNote = $showHostingNote;

        return $this;
    }

    public function getPriceNote(): ?string
    {
        return $this->priceNote;
    }

    public function setPriceNote(?string $priceNote): static
    {
        $this->priceNote = $priceNote;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return PriceMention[]
     */
    public function getPriceMentions(): array
    {
        return array_values(array_filter([
            $this->showStartingFrom ? PriceMention::StartingFrom : null,
            $this->showHostingNote ? PriceMention::HostingNote : null,
        ]));
    }
}
