<?php

namespace HealthCheckBundle\Tests\Integration\Repository;

use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Repository\SqlCheckerRepository;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;

class SqlCheckerRepositoryTest extends TestCase
{
    public function testRepositoryClassExists(): void
    {
        $this->assertTrue(class_exists(SqlCheckerRepository::class));
    }

    public function testRepositoryCanBeInstantiated(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SqlCheckerRepository($registry);
        
        $this->assertInstanceOf(SqlCheckerRepository::class, $repository);
    }


    public function testRepositoryInheritance(): void
    {
        $reflection = new \ReflectionClass(SqlCheckerRepository::class);
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $reflection->getParentClass()->getName());
    }
}