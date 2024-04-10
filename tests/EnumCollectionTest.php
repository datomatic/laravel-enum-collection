<?php

use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\IntBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\PureEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\StringBackedEnum;

test('enumCollection can accept an array of enums', function ($from, array $results) {
    $enumCollection = EnumCollection::from($from);
    $enumCollection2 = EnumCollection::tryFrom($from);

    expect($enumCollection)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection->toArray())->toEqual($results);
    expect($enumCollection2)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection2->toArray())->toEqual($results);
})->with([
    'enum single' => [PureEnum::BLACK, [PureEnum::BLACK]],
    'enum array' => [[PureEnum::BLACK, PureEnum::GREEN], [PureEnum::BLACK, PureEnum::GREEN]],
    'string enum array' => [[StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM], [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM]],
    'int enum array' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC], [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
]);

test('enumCollection throws an exception if an enum class is not set and an array of values/names is passed', function ($from) {
    expect(fn () => EnumCollection::from($from))->toThrow(Exception::class);
    expect(fn () => EnumCollection::tryFrom($from))->toThrow(Exception::class);
})->with([
    'enum single' => ['BLACK'],
    'enum array' => [['BLACK', 'GREEN']],
    'string enum array' => [['S', 'M', 'L']],
    'int enum array' => [[1, 2, 3]],
]);

test('enumCollection can accept an array of enums values and names', function ($from, string $enumClass, array $results) {
    $enumCollection = EnumCollection::of($enumClass)->from($from);
    $enumCollection2 = EnumCollection::of($enumClass)->tryFrom($from);

    expect($enumCollection)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection->toArray())->toEqual($results);
    expect($enumCollection2)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection2->toArray())->toEqual($results);
})->with([
    'enum single' => ['BLACK', PureEnum::class, [PureEnum::BLACK]],
    'enum array' => [['BLACK', 'GREEN'], PureEnum::class, [PureEnum::BLACK, PureEnum::GREEN]],
    'string enum array' => [['S', 'M', 'M'], StringBackedEnum::class, [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM]],
    'string enum array2' => [['SMALL', 'MEDIUM', 'MEDIUM'], StringBackedEnum::class, [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM]],
    'int enum array' => [[1, 2], IntBackedEnum::class, [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'int enum array2' => [['1', '2'], IntBackedEnum::class, [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'int enum array3' => [['PRIVATE', 'PUBLIC'], IntBackedEnum::class, [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
]);

test('enumCollection throws an exception if wrong className passed with from method', function ($from, string $enumClass) {
    expect(fn () => EnumCollection::of($enumClass)->from($from))->toThrow(ValueError::class);
})->with([
    'enum single' => ['BLACK', StringBackedEnum::class],
    'enum array' => [['BLACK', 'GREEN'], IntBackedEnum::class],
    'string enum array' => [['S', 'M', 'M'], PureEnum::class],
    'int enum array' => [[1, 2], PureEnum::class],
]);

test('enumCollection doesnt throws an exception if wrong className passed with tryFrom method', function ($from, string $enumClass) {
    expect(fn () => EnumCollection::of($enumClass)->tryFrom($from))->not->toThrow(ValueError::class);
})->with([
    'enum single' => ['BLACK', StringBackedEnum::class],
    'enum array' => [['BLACK', 'GREEN'], IntBackedEnum::class],
    'string enum array' => [['S', 'M', 'M'], PureEnum::class],
    'int enum array' => [[1, 2], PureEnum::class],
]);

test('enumCollection throws an exception if wrong value/name passed with from method', function ($from, string $enumClass) {
    expect(fn () => EnumCollection::of($enumClass)->from($from))->toThrow(ValueError::class);
})->with([
    'enum single' => ['SS', StringBackedEnum::class],
    'enum array' => [['EFF', '3493400'], IntBackedEnum::class],
    'string enum array' => [['XC', 'M', 'M'], PureEnum::class],
    'int enum array' => [[33, 2], PureEnum::class],
]);

test('enumCollection throws an exception if wrong value/name passed with tryFrom method', function ($from, string $enumClass) {
    expect(fn () => EnumCollection::of($enumClass)->tryFrom($from))->not->toThrow(ValueError::class);
})->with([
    'enum single' => ['SS', StringBackedEnum::class],
    'enum array' => [['EFF', '3493400'], IntBackedEnum::class],
    'string enum array' => [['XC', 'M', 'M'], PureEnum::class],
    'int enum array' => [[33, 2], PureEnum::class],
]);

it('can enumCollection get enumClass ', function (?string $enumClass) {
    expect(EnumCollection::of($enumClass)->getEnumClass())->toEqual($enumClass);
    expect((new EnumCollection)->setEnumClass($enumClass)->getEnumClass())->toEqual($enumClass);
})->with([
    'null' => [null],
    'base enum' => [PureEnum::class],
    'string enum array' => [StringBackedEnum::class],
    'int enum array' => [IntBackedEnum::class],
]);

test('enumCollection toValues method ', function ($from, ?string $enumClass, array $results) {
    expect(EnumCollection::from($from, $enumClass)->toValues())->toEqual($results);
    expect(EnumCollection::of($enumClass)->from($from)->toValues())->toEqual($results);
    expect(EnumCollection::tryFrom($from, $enumClass)->toValues())->toEqual($results);
    expect(EnumCollection::of($enumClass)->tryFrom($from)->toValues())->toEqual($results);
})->with([
    'enum single' => ['BLACK', PureEnum::class, ['BLACK']],
    'enum single2' => [PureEnum::BLACK, null, ['BLACK']],
    'enum array' => [['BLACK', 'GREEN'], PureEnum::class, ['BLACK', 'GREEN']],
    'enum array2' => [[PureEnum::BLACK, PureEnum::GREEN], null, ['BLACK', 'GREEN']],
    'string enum array' => [['S', 'M', 'M'], StringBackedEnum::class, ['S', 'M', 'M']],
    'string enum array2' => [['SMALL', 'MEDIUM', 'MEDIUM'], StringBackedEnum::class, ['S', 'M', 'M']],
    'string enum array3' => [[StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM], null, ['S', 'M', 'M']],
    'int enum array' => [[1, 2], IntBackedEnum::class, [1, 2]],
    'int enum array2' => [['1', '2'], IntBackedEnum::class, [1, 2]],
    'int enum array3' => [['PRIVATE', 'PUBLIC'], IntBackedEnum::class, [1, 2]],
    'int enum array4' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC], null, [1, 2]],
]);

it('will can check if EnumCollection contains enum', function ($from, $search, $result) {
    $enumCollection = EnumCollection::from($from);
    $enumCollection2 = EnumCollection::tryFrom($from);

    expect($enumCollection->contains($search))->toEqual($result);
    expect($enumCollection->doesntContain($search))->toEqual(! $result);
    expect($enumCollection2->contains($search))->toEqual($result);
    expect($enumCollection2->doesntContain($search))->toEqual(! $result);
})->with([
    'pure enum collection search value' => [[PureEnum::GREEN, PureEnum::BLACK], 'GREEN', true],
    'pure enum collection search invalid value' => [[PureEnum::GREEN, PureEnum::BLACK], 'PURPLE', false],
    'pure enum collection search invalid value int' => [[PureEnum::GREEN, PureEnum::BLACK], 1, false],
    'pure enum collection search enum' => [[PureEnum::GREEN, PureEnum::BLACK], PureEnum::BLACK, true],
    'pure enum collection search invalid enum' => [[PureEnum::GREEN, PureEnum::BLACK], PureEnum::YELLOW, false],
    'pure enum collection search name' => [[PureEnum::GREEN, PureEnum::BLACK], 'BLACK', true],
    'pure enum collection search invalid name' => [[PureEnum::GREEN, PureEnum::BLACK], 'YELLOW', false],

    'int enum collection search value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 1, true],
    'int enum collection search value string' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], '3', true],
    'int enum collection search invalid value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'A', false],
    'int enum collection search invalid value2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 4, false],
    'int enum collection search enum' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PROTECTED, true],
    'int enum collection search invalid enum' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PUBLIC, false],
    'int enum collection search name' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PROTECTED', true],
    'int enum collection search invalid name' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PUBLIC', false],

    'string enum collection search value' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'L', true],
    'string enum collection search invalid value' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'LD', false],
    'string enum collection search invalid value int' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 4, false],
    'string enum collection search enum' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::EXTRA_LARGE, true],
    'string enum collection search invalid enum' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::SMALL, false],
    'string enum collection search name' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'EXTRA_LARGE', true],
    'string enum collection search invalid name' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'SMALL', false],
]);

it('forwards call to underlying collection', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection->count())->toEqual(2);
    expect($collection->contains('GREEN'))->toEqual(true);
    expect($collection->contains('PURPLE'))->toEqual(false);
});

it('throws on call to non existent method', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection->foo();
})->throws(BadMethodCallException::class);

it('throws on call to non existent static method', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection::foo();
})->throws(BadMethodCallException::class);
