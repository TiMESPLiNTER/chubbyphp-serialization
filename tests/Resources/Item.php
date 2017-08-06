<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Serialization\Resources;

final class Item
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array[]
     */
    private $treeValues = [];

    /**
     * @var float
     */
    private $progress;

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array[]
     */
    public function getTreeValues(): array
    {
        return $this->treeValues;
    }

    /**
     * @param array[] $treeValues
     *
     * @return self
     */
    public function setTreeValues(array $treeValues): self
    {
        $this->treeValues = $treeValues;

        return $this;
    }

    /**
     * @return float
     */
    public function getProgress(): float
    {
        return $this->progress;
    }

    /**
     * @param float $progress
     *
     * @return self
     */
    public function setProgress(float $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return self
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
