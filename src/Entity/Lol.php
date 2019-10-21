<?php

namespace App\Entity;

use App\Model\Twitter;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LolRepository")
 * @UniqueEntity(
 *     fields={"imageUrl"},
 *     errorPath="imageUrl",
 *     message="Already saved"
 * )
 */
class Lol implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $caption;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $fetched;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $videoSources = [];

    /** @var string */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getFetched(): ?\DateTimeImmutable
    {
        return $this->fetched;
    }

    public function setFetched(\DateTimeImmutable $fetched): self
    {
        $this->fetched = $fetched;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $content
     * @return Lol
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    public function getContent(): string
    {
        return $this->content ?? sprintf('<img src="%s" title="%s"/>', $this->getImageUrl(), $this->getCaption());
    }

    public function getVideoSources(): ?array
    {
        return $this->videoSources;
    }

    public function setVideoSources(?array $videoSources): self
    {
        $this->videoSources = $videoSources;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'content' => $this->getContent(),
            'url' => $this->getUrl(),
            'fetched' => $this->getFetched(),
            'imageUrl' => $this->getImageUrl(),
            'videoSources' => $this->getVideoSources(),
            'caption' => $this->getCaption(),
            'title' => $this->getTitle(),
        ];
    }

    /**
     * @return ObjectType
     */
    public static function getGraphQlDefinition(): ObjectType
    {
        $lolType = new ObjectType([
            'name' => 'Lol',
            'fields' => [
                'content' => [
                    'type' => Type::nonNull(Type::string())
                ],
                'url' => [
                    'type' => Type::nonNull(Type::string())
                ],
                'fetched' => [
                    'type' => Type::nonNull(Type::int())
                ],
                'imageUrl' => [
                    'type' => Type::string()
                ],
                'videoSources' => [
                    'type' => Type::listOf(Type::string())
                ],
                'caption' => [
                    'type' => Type::string()
                ],
                'title' => [
                    'type' => Type::nonNull(Type::string())
                ],
            ]
        ]);
        return $lolType;
    }
}
