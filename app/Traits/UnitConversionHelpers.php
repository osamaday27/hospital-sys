<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use App\Services\SupplyChain\UnitConversionService;

trait UnitConversionHelpers
{
    /** احصل على الخدمة من الـContainer */
    protected function unitSvc(): UnitConversionService
    {
        return app(UnitConversionService::class);
    }

    /** كل وحدات الصنف مع العوامل والأسعار */
    protected function ucUnits(int $productId, string $nameColumn = 'name_ar'): Collection
    {
        return $this->unitSvc()->unitsForProduct($productId, $nameColumn);
    }

    /** تحويل كمية بين وحدتين */
    protected function ucConvert(int $productId, float $qty, int $fromUnitId, int $toUnitId): float
    {
        return $this->unitSvc()->convert($productId, $qty, $fromUnitId, $toUnitId);
    }

    /** تطبيع الكمية إلى أصغر وحدة (قاعدة التخزين) */
    protected function ucToBase(int $productId, float $qty, int $unitId): float
    {
        return $this->unitSvc()->toBase($productId, $qty, $unitId);
    }

    /** تحويل من أصغر وحدة إلى وحدة هدف */
    protected function ucFromBase(int $productId, float $qtyBase, int $toUnitId): float
    {
        return $this->unitSvc()->fromBase($productId, $qtyBase, $toUnitId);
    }

    /** إرجاع ID أصغر وحدة للصنف */
    protected function ucBaseUnitId(int $productId): ?int
    {
        return $this->unitSvc()->smallestUnitId($productId);
    }

    /**
     * إجمالي المتاح بأي وحدة (مع فلاتر اختيارية: مخزن/باتش/صلاحية)
     * مرّر تاريخ الانتهاء بصيغة YYYY-MM-DD إن أردت التقييد.
     */
    protected function ucAvailable(
        int $productId,
        int $targetUnitId,
        ?int $storeId = null,
        ?string $batchNo = null,
        ?string $expiryDate = null
    ): float {
        return $this->unitSvc()->getAvailableQtyByUnit($productId, $targetUnitId, $storeId, $batchNo, $expiryDate);
    }

    /** تفكيك كمية (بالأصغر) إلى أجزاء للعرض: 3 دستة + 1 باكيت + … */
    protected function ucDecompose(int $productId, float $qtyBase, string $nameColumn = 'name_ar'): array
    {
        return $this->unitSvc()->decomposeFromBase($productId, $qtyBase, $nameColumn);
    }
}
