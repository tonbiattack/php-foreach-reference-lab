<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/OrderStatusNormalizer.php';

use PhpForeachReferenceLab\OrderStatusNormalizer;

/**
 * @param mixed $expected
 * @param mixed $actual
 */
function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message
            . "\nExpected: " . var_export($expected, true)
            . "\nActual:   " . var_export($actual, true)
        );
    }
}

/**
 * @return list<array{id: string, status: string}>
 */
function fixtureOrders(): array
{
    return [
        ['id' => 'order-100', 'status' => 'paid'],
        ['id' => 'order-200', 'status' => 'pending'],
        ['id' => 'order-300', 'status' => 'paid'],
    ];
}

$normalizer = new OrderStatusNormalizer();

$tests = [
    '監査ループがなければ、参照付きループで正規化した結果は保持される' => static function () use ($normalizer): void {
        $actual = $normalizer->normalizeOnly(fixtureOrders());

        assertSameValue(
            [
                ['id' => 'order-100', 'status' => 'settled'],
                ['id' => 'order-200', 'status' => 'pending'],
                ['id' => 'order-300', 'status' => 'settled'],
            ],
            $actual,
            '正規化処理そのものが末尾レコードを破損していないこと'
        );
    },
    '読み取り専用の監査ループを通しても、正規化済みの末尾レコードは保持される' => static function () use ($normalizer): void {
        $actual = $normalizer->normalizeAndBuildAudit(fixtureOrders());

        assertSameValue(
            [
                ['id' => 'order-100', 'status' => 'settled'],
                ['id' => 'order-200', 'status' => 'pending'],
                ['id' => 'order-300', 'status' => 'settled'],
            ],
            $actual,
            '監査用の読み取りループは注文レコードを変更してはならない'
        );
    },
];

$failures = [];

foreach ($tests as $name => $test) {
    try {
        $test();
        fwrite(STDOUT, "PASS: {$name}\n");
    } catch (Throwable $error) {
        $failures[] = $name;
        fwrite(STDERR, "FAIL: {$name}\n{$error->getMessage()}\n");
    }
}

if ($failures !== []) {
    fwrite(STDERR, sprintf("%d test(s) failed.\n", count($failures)));
    exit(1);
}

fwrite(STDOUT, sprintf("%d test(s) passed.\n", count($tests)));
