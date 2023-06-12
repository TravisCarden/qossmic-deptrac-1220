<?php declare(strict_types=1);

namespace QossmicDeptrac1220\Domain;

use \PHP_CodeSniffer\Runner as _VendorClass;

final class Bad_DependOnVendor
{
    public function call(): string
    {
        return _VendorClass::class;
    }
}
