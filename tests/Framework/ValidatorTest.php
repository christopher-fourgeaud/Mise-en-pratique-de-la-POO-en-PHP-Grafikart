<?php

namespace Tests\Framework;

use Framework\Validator;
use Tests\DatabaseTestCase;

class ValidatorTest extends DatabaseTestCase
{
    private function makeValidator(array $params)
    {
        return new Validator($params);
    }

    public function testRequiredIfFail()
    {
        $errors = $this->makeValidator([
            'name' => 'joe',
        ])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testRequiredIfSuccess()
    {
        $errors = $this->makeValidator([
            'name' => 'joe',
            'content' => 'content'
        ])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testNotEmpty()
    {
        $errors = $this->makeValidator([
            'name' => 'joe',
            'content' => ''
        ])
            ->notEmpty('content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-azeaze34',
            'slug2' => 'azeaz',
        ])
            ->checkSlug('slug')
            ->checkSlug('slug2')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-azAaze34',
            'slug2' => 'aze-aze_azeAze34',
            'slug3' => 'aze--aze-aze',
            'slug4' => 'aze--aze-aze-',
        ])
            ->checkSlug('slug')
            ->checkSlug('slug2')
            ->checkSlug('slug3')
            ->checkSlug('slug4')

            ->getErrors();

        $this->assertEquals(['slug', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testLength()
    {
        $params = [
            'slug' => '123456789',
        ];

        $this->assertCount(0, $this->makeValidator($params)
            ->checkLength('slug', 3)
            ->getErrors());

        $errors = $this->makeValidator($params)
            ->checkLength('slug', 12)
            ->getErrors();

        $this->assertCount(1, $errors);

        $this->assertCount(1, $this->makeValidator($params)
            ->checkLength('slug', 3, 4)
            ->getErrors());

        $this->assertCount(0, $this->makeValidator($params)
            ->checkLength('slug', 3, 20)
            ->getErrors());

        $this->assertCount(0, $this->makeValidator($params)
            ->checkLength('slug', null, 20)
            ->getErrors());

        $this->assertCount(1, $this->makeValidator($params)
            ->checkLength('slug', null, 8)
            ->getErrors());
    }

    public function testDateTime()
    {
        $params = ['date' => '2012-12-12 11:12:13'];
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->checkDatetime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->checkDatetime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->checkDatetime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->checkDatetime('date')->getErrors());
    }

    public function testCheckExists()
    {
        $pdo = $this->getPDO();
        $pdo->exec(
            "CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255)
            )"
        );

        $pdo->exec(
            "INSERT INTO test (name)
            VALUES ('a1')"
        );

        $pdo->exec(
            "INSERT INTO test (name)
            VALUES ('a2')"
        );

        $this->assertTrue($this->makeValidator(['category' => 1])->checkExists('category', 'test', $pdo)->isValid());
        $this->assertFalse($this->makeValidator(['category' => 2132132])->checkExists('category', 'test', $pdo)->isValid());
    }

    public function testCheckUnique()
    {
        $pdo = $this->getPDO();
        $pdo->exec(
            "CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255)
            )"
        );

        $pdo->exec(
            "INSERT INTO test (name)
            VALUES ('a1')"
        );

        $pdo->exec(
            "INSERT INTO test (name)
            VALUES ('a2')"
        );

        $this->assertFalse($this->makeValidator(['name' => 'a1'])->checkUnique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a111'])->checkUnique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a1'])->checkUnique('name', 'test', $pdo, 1)->isValid());
        $this->assertFalse($this->makeValidator(['name' => 'a2'])->checkUnique('name', 'test', $pdo, 1)->isValid());
    }
}
