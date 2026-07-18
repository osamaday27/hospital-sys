<?php

namespace App\services\Supplier;

use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierInvoiceItem;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceService
{
    /**
     * إنشاء فاتورة جديدة للمورد
     *
     * @param array $invoiceData بيانات الفاتورة الرئيسية
     * @param array $itemsData   تفاصيل الأصناف أو الخدمات
     * @return SupplierInvoice
     * @throws \Exception
     */
    public function createInvoice(array $invoiceData, array $itemsData)
    {
        return DB::transaction(function () use ($invoiceData, $itemsData) {

            // إنشاء الفاتورة
            $invoice = SupplierInvoice::create($invoiceData);

            $subtotal = 0;
            $taxTotal = 0;
            $discountTotal = 0;

            foreach ($itemsData as $item) {
                $itemTotal = ($item['qty'] * $item['unit_price']) - $item['discount'] + $item['tax'];

                SupplierInvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_type'       => $item['item_type'] ?? 'product',
                    'product_id'      => $item['product_id'] ?? null,
                    'product_unit_id' => $item['product_unit_id'] ?? null,
                    'service_id'      => $item['service_id'] ?? null,
                    'description'     => $item['description'] ?? null,
                    'qty'             => $item['qty'],
                    'unit_price'      => $item['unit_price'],
                    'discount'        => $item['discount'] ?? 0,
                    'tax'             => $item['tax'] ?? 0,
                    'total'           => $itemTotal,
                ]);

                $subtotal     += $item['qty'] * $item['unit_price'];
                $discountTotal += $item['discount'] ?? 0;
                $taxTotal     += $item['tax'] ?? 0;
            }

            // تحديث بيانات الفاتورة المالية
            $invoice->update([
                'subtotal'        => $subtotal,
                'discount'        => $discountTotal,
                'tax'             => $taxTotal,
                'total_amount'    => $subtotal - $discountTotal + $taxTotal,
                'remaining_amount'=> $subtotal - $discountTotal + $taxTotal, // لسه مدفوع صفر
            ]);

            return $invoice->fresh(['items']);
        });
    }
}
