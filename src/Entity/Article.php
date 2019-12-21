<?php

namespace App\Entity;

use App\Services\AntiSpam\AntiSpam;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(name="article")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(message="Le nombre de vue(s) ne peut pas être inférieur à 0")
     */
    private $nbViews;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="articles")
     * @JoinTable(name="asso_article_category")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="favourite_articles")
     */
    private $users_who_like;

    public function __construct()
    {
        $this->setNbViews(0);
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->users_who_like = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Article
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $thumbnail
     * @return Article
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getNbViews(): ?int
    {
        return $this->nbViews;
    }

    public function setNbViews(int $nbViews): self
    {
        $this->nbViews = $nbViews;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);

            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

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

    /**
     * @return Collection|User[]
     */
    public function getUsersWhoLike(): Collection
    {
        return $this->users_who_like;
    }

    public function addUsersWhoLike(User $usersWhoLike): self
    {
        if (!$this->users_who_like->contains($usersWhoLike)) {
            $this->users_who_like[] = $usersWhoLike;
            $usersWhoLike->addFavouriteArticle($this);
        }

        return $this;
    }

    public function removeUsersWhoLike(User $usersWhoLike): self
    {
        if ($this->users_who_like->contains($usersWhoLike)) {
            $this->users_who_like->removeElement($usersWhoLike);
            $usersWhoLike->removeFavouriteArticle($this);
        }

        return $this;
    }
    
    /**
     * @Assert\Callback()
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        if($this->title == $this->content) {
            $context->buildViolation("Le contenu ne peut pas être identique au titre")
                ->atPath("content")
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback()
     */
    public function isAtLeastOneCategory(ExecutionContextInterface $context)
    {
        if($this->getCategories()->isEmpty()) {
            $context->buildViolation("L'article doit contenir au moins une catégorie")
                ->atPath("categories")
                ->addViolation();
        }
    }

    /**
     * @ORM\PrePersist()
     */
    public function forceAuthor()
    {
        if (is_null($this->author)) {
            $this->author = "Anonymous";
        }
    }

    /**
     * @ORM\PreUpdate()
     * @throws \Exception
     */
    public function forceUpdatedAt()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * @ORM\PrePersist()
     * @throws \Exception
     */
    public function forceImageAndThumbnail()
    {
        if (is_null($this->image)) {
            $category = $this->getCategories()[0]->getName();
            $random = random_int(1, 10);
            $url = 'images/' . $category . '/' . $random . '.jpg';
            $this->image = $url;
            $this->thumbnail = $url;
        }
    }
}
