<?php

namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
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
}
