<?php

declare(strict_types=1);

namespace PhpForeachReferenceLab;

final class OrderStatusNormalizer
{
    /**
     * @param list<array{id: string, status: string}> $orders
     * @return list<array{id: string, status: string}>
     */
    public function normalizeAndBuildAudit(array $orders): array
    {
        foreach ($orders as &$order) {
            if ($order['status'] === 'paid') {
                $order['status'] = 'settled';
            }
        }
        unset($order); // 最後の要素への参照を切る。

        // 意図: 読み取り専用の監査ログ用ループ。
        foreach ($orders as $order) {
            $this->writeAuditLine($order);
        }

        return $orders;
    }

    /**
     * @param list<array{id: string, status: string}> $orders
     * @return list<array{id: string, status: string}>
     */
    public function normalizeOnly(array $orders): array
    {
        foreach ($orders as &$order) {
            if ($order['status'] === 'paid') {
                $order['status'] = 'settled';
            }
        }

        return $orders;
    }

    /**
     * @param array{id: string, status: string} $order
     */
    private function writeAuditLine(array $order): void
    {
        // 本番ではロガーへ書き込む想定。再現を決定的にするため副作用を省略する。
    }
}
