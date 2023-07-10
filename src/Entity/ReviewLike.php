<?php

namespace App\Entity;

use App\Repository\ReviewLikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=ReviewLikeRepository::class)
 */
class ReviewLike
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Review::class, inversedBy="likes")
     * @Assert\NotBlank
     * @ORM\JoinColumn(onDelete="CASCADE") 
     */
    private $review;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likes")
     * @Assert\NotBlank
     * @ORM\JoinColumn(onDelete="CASCADE") 
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
