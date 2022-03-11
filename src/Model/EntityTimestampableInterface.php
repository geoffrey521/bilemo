<?php

namespace App\Model;

interface EntityTimestampableInterface
{
    public function setCreatedAt(): self;

    public function getCreatedAt(): ?\DateTimeImmutable;

    public function setUpdatedAt(): self;

    public function getUpdatedAt(): ?\DateTimeImmutable;

}
