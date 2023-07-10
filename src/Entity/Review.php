<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reviews")
     * @ORM\JoinColumn(onDelete="CASCADE") 
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class, inversedBy="reviews")
     * @ORM\JoinColumn(onDelete="CASCADE") 
     */
    private $album;

    /**
     * @ORM\OneToMany(targetEntity=ReviewLike::class, mappedBy="review")
     * @ORM\JoinColumn(onDelete="CASCADE") 
     */
    private $likes;

    public function __construct() 
    {
        $this->publishedAt = new \DateTime();
        $this->likes = new ArrayCollection();   
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
    $dateTimeNow = new DateTime('now');

    $this->setUpdatedAt($dateTimeNow);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }


      /**
     * Cette function sera éxécutée avant chaque persist de notre entité
     * 
     * ! Pour cela il faut mettre l'annotation sur la classe : ORM\HasLifecycleCallbacks
     * 
     * @ORM\PrePersist
     */
    public function autoAlbumUpdatedAt()
    {
        $this->album->setUpdatedAt(new DateTime('now'));
    }
 
    /**
     * @return Collection<int, ReviewLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(ReviewLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setReview($this);
        }

        return $this;
    }

    public function removeLike(ReviewLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getReview() === $this) {
                $like->setReview(null);
            }
        }

        return $this;
    }

    /**
     * permet de savoir si la review est liké par un utilisateur
     *
     * @return boolean
     */
    public function isLikedByUser(User $user) : bool 
    {
        foreach($this->likes as $like) {
            if($like->getUser() === $user) return true;
        }
        return false;
    }
}
