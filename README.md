# Reproducer for Issue #1220 · qossmic/deptrac

This repository is an attempt to reproduce the issue from https://github.com/qossmic/deptrac/issues/1220.

It fails to reproduce it, i.e., it produces the expected result, not the problem reported in the issue.

## Setup

```shell
git clone git@github.com:TravisCarden/qossmic-deptrac-1220.git
cd qossmic-deptrac-1220
composer install
```

## Overview

### Project files

```shell
$ tree --gitignore
.
├── README.md
├── composer.json
├── composer.lock
├── deptrac.yaml
├── src
│   ├── Domain
│   │   ├── Bad_DependOnInfrastructure.php
│   │   ├── Bad_DependOnTest.php
│   │   ├── Bad_DependOnVendor.php
│   │   ├── Good_DependOnDomain.php
│   │   └── _DomainClass.php
│   └── Infrastructure
│       ├── Bad_DependOnTest.php
│       ├── Good_DependOnDomain.php
│       ├── Good_DependOnInfrastructure.php
│       ├── Good_DependOnVendor.php
│       └── _InfrastructureClass.php
└── tests
    ├── Good_DependOnDomain.php
    ├── Good_DependOnInfrastructure.php
    ├── Good_DependOnTest.php
    ├── Good_DependOnVendor.php
    └── _TestClass.php

5 directories, 19 files
```

### Deptrac configuration

[`deptrac.yaml`](deptrac.yaml)

```shell
$ cat deptrac.yaml
deptrac:

  paths:
    - ./src
    - ./tests
    - ./vendor

  layers:
    -
      name: Domain
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/src/Domain/.*'
    -
      name: Infrastructure
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/src/Infrastructure/.*'
    -
      name: Tests
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/tests/.*'
    -
      name: Vendor
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/vendor/.*'

  ruleset:

    Domain: ~

    Infrastructure:
      - Domain
      - Vendor

    Tests:
      - Domain
      - Infrastructure
      - Vendor

    Vendor: ~
```

## Results

```shell
$ ./vendor/bin/deptrac
 1423/1423 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 ----------- ---------------------------------------------------------------------------------------------------------------------------------------------
  Reason      Infrastructure
 ----------- ---------------------------------------------------------------------------------------------------------------------------------------------
  Violation   QossmicDeptrac1220\Infrastructure\Bad_DependOnTest must not depend on QossmicDeptrac1220\Tests\_TestClass (Tests)
              /var/www/qossmic-deptrac-1220/src/Infrastructure/Bad_DependOnTest.php:11
  Violation   QossmicDeptrac1220\Infrastructure\Bad_DependOnTest must not depend on QossmicDeptrac1220\Tests\_TestClass (Tests)
              /var/www/qossmic-deptrac-1220/src/Infrastructure/Bad_DependOnTest.php:5
 ----------- ---------------------------------------------------------------------------------------------------------------------------------------------

 ----------- ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Reason      Domain
 ----------- ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Violation   QossmicDeptrac1220\Domain\Bad_DependOnVendor must not depend on PHPUnit\Event\Runtime\PHPUnit (Vendor)
              /var/www/qossmic-deptrac-1220/src/Domain/Bad_DependOnVendor.php:11
  Violation   QossmicDeptrac1220\Domain\Bad_DependOnVendor must not depend on PHPUnit\Event\Runtime\PHPUnit (Vendor)
              /var/www/qossmic-deptrac-1220/src/Domain/Bad_DependOnVendor.php:5
  Violation   QossmicDeptrac1220\Domain\Bad_DependOnInfrastructure must not depend on QossmicDeptrac1220\Infrastructure\_InfrastructureClass (Infrastructure)
              /var/www/qossmic-deptrac-1220/src/Domain/Bad_DependOnInfrastructure.php:11
  Violation   QossmicDeptrac1220\Domain\Bad_DependOnInfrastructure must not depend on QossmicDeptrac1220\Infrastructure\_InfrastructureClass (Infrastructure)
              /var/www/qossmic-deptrac-1220/src/Domain/Bad_DependOnInfrastructure.php:5
  Violation   QossmicDeptrac1220\Domain\Bad_DependOnTest must not depend on QossmicDeptrac1220\Tests\_TestClass (Tests)
              /var/www/qossmic-deptrac-1220/src/Domain/Bad_DependOnTest.php:11
  Violation   QossmicDeptrac1220\Domain\Bad_DependOnTest must not depend on QossmicDeptrac1220\Tests\_TestClass (Tests)
              /var/www/qossmic-deptrac-1220/src/Domain/Bad_DependOnTest.php:5
 ----------- ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------


 -------------------- -----
  Report
 -------------------- -----
  Violations           8
  Skipped violations   0
  Uncovered            15
  Allowed              10
  Warnings             0
  Errors               0
 -------------------- -----
```

## Debug

```shell
$ ./vendor/bin/deptrac debug:layer
 ------------------------------------------------------
  Domain
 ------------------------------------------------------
  QossmicDeptrac1220\Domain\Bad_DependOnInfrastructure
  QossmicDeptrac1220\Domain\Bad_DependOnTest
  QossmicDeptrac1220\Domain\Bad_DependOnVendor
  QossmicDeptrac1220\Domain\Good_DependOnDomain
  QossmicDeptrac1220\Domain\_DomainClass
 ------------------------------------------------------

 ---------------------------------------------------------------
  Infrastructure
 ---------------------------------------------------------------
  QossmicDeptrac1220\Infrastructure\Bad_DependOnTest
  QossmicDeptrac1220\Infrastructure\Good_DependOnDomain
  QossmicDeptrac1220\Infrastructure\Good_DependOnInfrastructure
  QossmicDeptrac1220\Infrastructure\Good_DependOnVendor
  QossmicDeptrac1220\Infrastructure\_InfrastructureClass
 ---------------------------------------------------------------

 ------------------------------------------------------
  Tests
 ------------------------------------------------------
  QossmicDeptrac1220\Tests\Good_DependOnDomain
  QossmicDeptrac1220\Tests\Good_DependOnInfrastructure
  QossmicDeptrac1220\Tests\Good_DependOnTest
  QossmicDeptrac1220\Tests\Good_DependOnVendor
  QossmicDeptrac1220\Tests\_TestClass
 ------------------------------------------------------

 ----------------------------------------------------------------------------------------------
  Vendor
 ----------------------------------------------------------------------------------------------
  ComposerAutoloaderInitef51b699a850cf2c8a5714e8cb48e8f9
  Composer\Autoload\ClassLoader
  Composer\Autoload\ComposerStaticInitef51b699a850cf2c8a5714e8cb48e8f9
  Composer\InstalledVersions
  DeepCopy\DeepCopy
  DeepCopy\Exception\CloneException
  DeepCopy\Exception\PropertyException
  ...
```
