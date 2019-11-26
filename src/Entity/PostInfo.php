<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostInfoRepository")
 */
class PostInfo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postInfos")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="postInfos")
     */
    private $post;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $readerAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $signAt;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param mixed $post
     */
    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Post
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function isReader(): bool
    {
        return $this->getReaderAt() !== null;
    }

    public function getReaderAt(): ?\DateTimeInterface
    {
        return $this->readerAt;
    }

    public function setReaderAt(?\DateTimeInterface $readerAt): self
    {
        $this->readerAt = $readerAt;

        return $this;
    }

    public function isSign(): bool
    {
        return $this->getSignAt() !== null;
    }

    public function getSignAt(): ?\DateTimeInterface
    {
        return $this->signAt;
    }

    public function setSignAt(?\DateTimeInterface $signAt): self
    {
        $this->signAt = $signAt;

        return $this;
    }
}
