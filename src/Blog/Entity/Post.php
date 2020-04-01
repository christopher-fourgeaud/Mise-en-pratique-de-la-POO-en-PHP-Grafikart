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

    public $image;


    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            $this->createdAt = new \DateTime($createdAt);
        }
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new \DateTime($updatedAt);
        }
    }

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }

    public function getImageUrl()
    {
        return '/uploads/posts/' . $this->image;
    }
}
