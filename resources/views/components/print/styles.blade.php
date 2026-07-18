
<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 15mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            color: #000;
            line-height: 1.5;
        }
        .no-print {
            display: none !important;
        }
        .page-break {
            page-break-after: always;
        }
    }

    .report-container {
        max-width: 100%;
        margin: 0 auto;
    }

    .report-header {
        text-align: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px double #444;
    }

    .hospital-info {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-bottom: 10px;
    }

    .hospital-logo {
        height: 90px;
        width: auto;
    }

    .hospital-text {
        text-align: center;
    }

    .hospital-name {
        font-size: 22px;
        font-weight: bold;
        color: #003366;
        margin-bottom: 5px;
    }

    .hospital-department {
        font-size: 16px;
        color: #555;
    }

    .report-title {
        font-size: 24px;
        font-weight: bold;
        color: #003366;
        margin: 15px 0 10px;
        text-decoration: underline;
        text-underline-offset: 8px;
    }

    .print-info {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #666;
        margin-top: 10px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background-color: #f8f9fa;
    }

    .header-table td {
        border: 1px solid #dee2e6;
        padding: 10px;
        font-size: 14px;
    }

    .header-table td strong {
        color: #003366;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
    }

    .items-table th {
        background-color: #003366;
        color: white;
        padding: 12px;
        border: 1px solid #ddd;
        font-weight: bold;
        text-align: center;
    }

    .items-table td {
        border: 1px solid #dee2e6;
        padding: 10px;
        text-align: center;
    }

    .items-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .expired {
        color: #dc3545;
        font-weight: bold;
    }

    .warning {
        color: #ffc107;
        font-weight: bold;
    }

    .total-row {
        font-weight: bold;
        background-color: #e7f1ff !important;
    }

    .signature-section {
        margin-top: 40px;
    }

    .signature-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .signature-table td {
        border: 1px solid #dee2e6;
        padding: 40px 10px 10px;
        text-align: center;
        position: relative;
    }

    .signature-line {
        position: absolute;
        bottom: 25px;
        left: 10%;
        width: 80%;
        height: 1px;
        background-color: #666;
    }

    .footer-notes {
        margin-top: 30px;
        padding-top: 15px;
        border-top: 1px dashed #999;
        font-size: 13px;
        color: #666;
        text-align: center;
    }

    .text-right {
        text-align: right !important;
    }

    .text-left {
        text-align: left !important;
    }

    .barcode {
        font-family: 'Libre Barcode 128', cursive;
        font-size: 30px;
        margin-top: 10px;
    }
</style>
