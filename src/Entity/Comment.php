<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    public const TYPE_AUDIO = 'audio';
    public const TYPE_VIDEO = 'video';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mediaLink;


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     */
    private $user;

    /**
     * @var Installation
     * @ORM\ManyToOne(targetEntity="Installation", inversedBy="comments")
     */
    private $installation;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Comment
     */
    public function setUser(?User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Installation
     */
    public function getInstallation(): Installation
    {
        return $this->installation;
    }

    /**
     * @param Installation $installation
     * @return Comment
     */
    public function setInstallation(Installation $installation): Comment
    {
        $this->installation = $installation;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param null|string $value
     * @return $this
     */
    public function setUsername($value)
    {
        $this->username = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMediaLink()
    {
        return $this->mediaLink;
    }

    /**
     * @param mixed $mediaLink
     */
    public function setMediaLink($mediaLink): void
    {
        $this->mediaLink = $mediaLink;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param bool $keyAsValue
     *
     * @return array
     */
    public static function getTypes($keyAsValue = false)
    {
        if ($keyAsValue) {
            return [
                ucwords(self::TYPE_AUDIO) => self::TYPE_AUDIO,
                ucwords(self::TYPE_VIDEO) => self::TYPE_VIDEO,
            ];
        }

        return [self::TYPE_AUDIO, self::TYPE_VIDEO];
    }
}
