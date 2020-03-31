<?php

namespace App\Blog\Entity;

use DateTime;

class Post
{
    public $id;

    public $name;

    public $slug;

    public $content;

    public $createdAt;

    public $updatedAt;

    public $categoryName;

    public $image;

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt): self
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTime($this->$createdAt);
        }

        return $this;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt): self
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new DateTime($this->$updatedAt);
        }

        return $this;
    }

    public function getThumb()
    {
        return '/uploads/posts/' . $this->image;
    }
}
