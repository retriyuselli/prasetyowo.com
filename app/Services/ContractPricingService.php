<?php

namespace App\Services;

use App\Models\SimulasiProduk;
use Illuminate\Support\Collection;

class ContractPricingService
{
    public function computeForSimulasiProduk(SimulasiProduk $record, ?Collection $items = null): array
    {
        $product = $record->product;

        $totalHargaPublish = 0;
        if ($items) {
            $totalHargaPublish = (float) $items->sum('price_public');
        }

        $baseTotalPrice = $record->getAttribute('total_price');
        if ($baseTotalPrice === null && $product) {
            $baseTotalPrice = $product->getAttribute('product_price');
            if (($baseTotalPrice === null || (float) $baseTotalPrice <= 0) && $items) {
                $baseTotalPrice = $items->sum('price_public');
            }
        }

        $productPenambahan = $record->getAttribute('penambahan');
        if ($productPenambahan === null && $product) {
            $productPenambahan = $product->getAttribute('penambahan_publish');
            if (($productPenambahan === null || (float) $productPenambahan <= 0) && $product?->penambahanHarga) {
                $productPenambahan = $product->penambahanHarga->sum('harga_publish');
            }
        }

        $productPengurangan = $record->getAttribute('pengurangan');
        if ($productPengurangan === null && $product) {
            $productPengurangan = $product->getAttribute('pengurangan');
            if (($productPengurangan === null || (float) $productPengurangan <= 0) && $product?->pengurangans) {
                $productPengurangan = $product->pengurangans->sum('amount');
            }
        }

        $promo = $record->getAttribute('promo');
        if ($promo === null) {
            $promo = 0;
        }

        $computedGrandTotal = OrderFinance::computeGrandTotalFromValues(
            (float) ($baseTotalPrice ?? 0),
            (float) ($productPenambahan ?? 0),
            (float) ($promo ?? 0),
            (float) ($productPengurangan ?? 0)
        );

        return [
            'product' => $product,
            'baseTotalPrice' => (float) ($baseTotalPrice ?? 0),
            'totalHargaPublish' => (float) ($totalHargaPublish ?? 0),
            'productPenambahan' => (float) ($productPenambahan ?? 0),
            'productPengurangan' => (float) ($productPengurangan ?? 0),
            'promo' => (float) ($promo ?? 0),
            'computedGrandTotal' => (float) $computedGrandTotal,
        ];
    }
}
