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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="postInfos")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", inversedBy="postInfos")
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
        $this->user = new ArrayCollection();
        $this->post = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Post $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post[] = $post;
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->post->contains($post)) {
            $this->post->removeElement($post);
        }

        return $this;
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
