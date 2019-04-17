<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests\Criteria\Support;

use Noitran\Repositories\Criteria\Support\FilterQueryParser;
use Noitran\Repositories\Tests\TestCase;

/**
 * Class FilterQueryParserTest.
 */
class FilterQueryParserTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_init_class(): void
    {
        $parser = new FilterQueryParser('profile.name', 'John');
        $this->assertInstanceOf(FilterQueryParser::class, $parser);
    }

    /**
     * @test
     */
    public function it_should_get_relation_and_column_name(): void
    {
        $parserWithRelation = (new FilterQueryParser('profile.name', 'John'))->parse();
        $this->assertEquals('profile', $parserWithRelation->getRelation());
        $this->assertEquals('name', $parserWithRelation->getColumn());

        $parserWithoutRelation = (new FilterQueryParser('name', 'John'))->parse();
        $this->assertNull($parserWithoutRelation->getRelation());
        $this->assertEquals('name', $parserWithRelation->getColumn());
    }

    /**
     * @test
     */
    public function it_should_get_logical_expression(): void
    {
        $parserWithRelation = (new FilterQueryParser('profile.name', ['$eq' => 'John']))->parse();
        $this->assertEquals('$eq', $parserWithRelation->getExpression());

        $parserWithRelation = (new FilterQueryParser('profile.name', ['$like' => '%John%']))->parse();
        $this->assertEquals('$like', $parserWithRelation->getExpression());
    }

    /**
     * @test
     */
    public function it_should_get_data_type_and_value(): void
    {
        $parser = (new FilterQueryParser('profile.name', 'John'))->parse();
        $this->assertEquals('John', $parser->getValue());
        $this->assertEquals('$string', $parser->getDataType());

        $parser = (new FilterQueryParser('profile.name', ['$eq' => 'John']))->parse();
        $this->assertEquals('John', $parser->getValue());
        $this->assertEquals('$string', $parser->getDataType());

        $parser = (new FilterQueryParser('profile.name', ['$eq' => '$string:John']))->parse();
        $this->assertEquals('John', $parser->getValue());
        $this->assertEquals('$string', $parser->getDataType());

        $parser = (new FilterQueryParser('profile.name', ['$eq' => '$int:John']))->parse();
        $this->assertEquals('John', $parser->getValue());
        $this->assertEquals('$int', $parser->getDataType());

        $parser = (new FilterQueryParser('profile.name', ['$eq' => '$unknown:John']))->parse();
        $this->assertEquals('John', $parser->getValue());
        $this->assertEquals('$string', $parser->getDataType());

        $parser = (new FilterQueryParser('profile.name', ['$eq' => '$int:some_data:another_data:John']))->parse();
        $this->assertEquals('some_data:another_data:John', $parser->getValue());
        $this->assertEquals('$int', $parser->getDataType());
    }
}
