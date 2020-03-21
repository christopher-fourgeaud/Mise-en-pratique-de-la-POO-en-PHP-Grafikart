<?php

namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use Tests\DatabaseTestCase;
use App\Blog\Table\PostTable;

class PostTableTest extends DatabaseTestCase
{
    /**
     * Instance de PostTable
     *
     * @var PostTable
     */
    private $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $post = $this->postTable->find(1);
        $this->assertNull($post);
    }

    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $this->postTable->update(1, [
            'name' => 'Salut',
            'slug' => 'demo',
        ]);

        $post = $this->postTable->find(1);

        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $this->postTable->insert([
            'name' => 'Salut',
            'slug' => 'demo'
        ]);

        $post = $this->postTable->find(1);

        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testDelete()
    {
        $this->postTable->insert([
            'name' => 'Salut',
            'slug' => 'demo'
        ]);
        $this->postTable->insert([
            'name' => 'Salut',
            'slug' => 'demo'
        ]);

        $count = $this->postTable->getPdo()->query(
            'SELECT COUNT(id)
            FROM posts'
        )->fetchColumn();

        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());

        $count = $this->postTable->getPdo()->query(
            'SELECT COUNT(id)
            FROM posts'
        )->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}
