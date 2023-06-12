<?php declare(strict_types=1);

namespace QossmicDeptrac1220\Infrastructure;

use QossmicDeptrac1220\Tests\_TestClass;

final class Bad_DependOnTest
{
    public function call(): string
    {
        return _TestClass::class;
    }
}
