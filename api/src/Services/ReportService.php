<?php
namespace App\Services;

class ReportService {
    public static function formatCurrency(float $value): string {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public static function summarizeFinances(float $balance, array $expenses): array {
        $totalExpenses = 0.0;
        foreach ($expenses as $item) {
            $totalExpenses += (float) ($item['total'] ?? 0);
        }

        return [
            'saldo_liquido' => $balance,
            'total_despesas' => $totalExpenses,
            'status' => $balance >= 0 ? 'positivo' : 'negativo',
            'categorias' => $expenses
        ];
    }
}