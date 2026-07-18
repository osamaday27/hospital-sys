<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>{{ __('محضر لجنة التخلص/الشطب') }} — {{ $row->request_no }}</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
      font-size: 14px;
      color: #1a1a1a;
      line-height: 1.6;
      background: #f5f5f5;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
      background: white;
      padding: 40px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
    }

    .page-header {
      border-bottom: 3px solid #6f42c1;
      padding-bottom: 20px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .page-header h1 {
      font-size: 24px;
      color: #6f42c1;
      font-weight: 600;
      flex: 1;
    }

    .request-number {
      background: #6f42c1;
      color: white;
      padding: 8px 20px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 16px;
    }

    .intro-text {
      background: #f8f9fa;
      border-right: 4px solid #6f42c1;
      padding: 15px 20px;
      margin-bottom: 30px;
      border-radius: 6px;
      color: #495057;
      font-size: 14px;
    }

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #6f42c1;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid #e0e0e0;
      display: flex;
      align-items: center;
    }

    .section-icon {
      margin-left: 10px;
      font-size: 20px;
    }

    .approvals-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      background: white;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .approvals-table thead {
      background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
      color: white;
    }

    .approvals-table th {
      padding: 14px 12px;
      font-weight: 600;
      text-align: center;
      border: 1px solid #5a32a3;
      font-size: 13px;
    }

    .approvals-table td {
      padding: 12px;
      border: 1px solid #dee2e6;
      text-align: center;
      vertical-align: middle;
    }

    .approvals-table tbody tr {
      transition: background 0.2s;
    }

    .approvals-table tbody tr:nth-child(even) {
      background: #f8f9fa;
    }

    .approvals-table tbody tr:hover {
      background: #f0e6ff;
    }

    .level-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      background: #6f42c1;
      color: white;
      border-radius: 50%;
      font-weight: 700;
      font-size: 14px;
    }

    .role-cell {
      text-align: right;
      font-weight: 600;
      color: #495057;
    }

    .assignee-cell {
      text-align: right;
      color: #6c757d;
    }

    .decision-badge {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .decision-approved {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .decision-rejected {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .decision-pending {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }

    .decision-conditional {
      background: #cfe2ff;
      color: #084298;
      border: 1px solid #b6d4fe;
    }

    .approver-cell {
      text-align: right;
      font-weight: 600;
      color: #28a745;
    }

    .date-cell {
      color: #6c757d;
      font-size: 13px;
    }

    .notes-cell {
      text-align: right;
      color: #6c757d;
      font-style: italic;
      max-width: 200px;
      word-wrap: break-word;
    }

    .empty-value {
      color: #adb5bd;
      font-style: italic;
    }

    .stats-section {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }

    .stat-card {
      flex: 1;
      min-width: 180px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 20px;
      border-radius: 8px;
      border-right: 4px solid #6f42c1;
      text-align: center;
    }

    .stat-value {
      font-size: 32px;
      font-weight: 700;
      color: #6f42c1;
      margin-bottom: 5px;
    }

    .stat-label {
      font-size: 13px;
      color: #6c757d;
      font-weight: 600;
    }

    .footer-section {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 2px solid #e0e0e0;
      text-align: center;
    }

    .print-button {
      background: #6f42c1;
      color: white;
      border: none;
      padding: 12px 30px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .print-button:hover {
      background: #5a32a3;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      transform: translateY(-1px);
    }

    .no-approvals {
      text-align: center;
      padding: 40px;
      color: #6c757d;
      background: #f8f9fa;
      border-radius: 8px;
      border: 2px dashed #dee2e6;
    }

    .no-approvals-icon {
      font-size: 48px;
      margin-bottom: 15px;
      opacity: 0.5;
    }

    @media print {
      body {
        background: white;
        padding: 0;
      }

      .container {
        box-shadow: none;
        padding: 20px;
        border-radius: 0;
      }

      .no-print {
        display: none !important;
      }

      .approvals-table tbody tr:hover {
        background: inherit;
      }

      .approvals-table {
        box-shadow: none;
      }

      .page-header {
        page-break-after: avoid;
      }

      table {
        page-break-inside: avoid;
      }

      .stat-card {
        break-inside: avoid;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- رأس الصفحة -->
  <div class="page-header">
    <h1>👥 {{ __('محضر لجنة التخلص/الشطب') }}</h1>
    <div class="request-number">
      {{ $row->request_no }}
    </div>
  </div>

  <!-- نص تمهيدي -->
  <div class="intro-text">
    <strong>📋 محضر اجتماع:</strong> يوثق هذا المحضر قرارات اللجنة والمخولين بشأن طلب التخلص/الشطب رقم <strong>{{ $row->request_no }}</strong>
  </div>

  @php
    $approvals = $row->approvals->sortBy('level');
    $totalApprovals = $approvals->count();
    $approvedCount = $approvals->where('decision', 'approved')->count();
    $rejectedCount = $approvals->where('decision', 'rejected')->count();
    $pendingCount = $approvals->where('decision', 'pending')->count();
  @endphp

  <!-- إحصائيات سريعة -->
  @if($totalApprovals > 0)
    <div class="stats-section no-print">
      <div class="stat-card">
        <div class="stat-value">{{ $totalApprovals }}</div>
        <div class="stat-label">{{ __('إجمالي المستويات') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" style="color: #28a745">{{ $approvedCount }}</div>
        <div class="stat-label">{{ __('موافق') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" style="color: #dc3545">{{ $rejectedCount }}</div>
        <div class="stat-label">{{ __('مرفوض') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" style="color: #ffc107">{{ $pendingCount }}</div>
        <div class="stat-label">{{ __('قيد الانتظار') }}</div>
      </div>
    </div>
  @endif

  <!-- عنوان القسم -->
  <h3 class="section-title">
    <span class="section-icon">📊</span>
    {{ __('سجل القرارات والموافقات') }}
  </h3>

  <!-- جدول الموافقات -->
  @if($totalApprovals > 0)
    <table class="approvals-table">
      <thead>
        <tr>
          <th style="width: 60px">{{ __('المستوى') }}</th>
          <th style="width: 180px">{{ __('اللجنة/الدور') }}</th>
          <th style="width: 140px">{{ __('المخوَّل') }}</th>
          <th style="width: 120px">{{ __('القرار') }}</th>
          <th style="width: 140px">{{ __('المعتمد') }}</th>
          <th style="width: 130px">{{ __('تاريخ القرار') }}</th>
          <th>{{ __('ملاحظات') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($approvals as $ap)
          <tr>
            <td>
              <span class="level-badge">{{ $ap->level }}</span>
            </td>
            <td class="role-cell">{{ $ap->role_or_committee ?? '-' }}</td>
            <td class="assignee-cell">{{ $ap->assigneeUser?->name ?? '-' }}</td>
            <td>
              @php
                $decisionClass = 'decision-pending';
                if($ap->decision === 'approved') $decisionClass = 'decision-approved';
                elseif($ap->decision === 'rejected') $decisionClass = 'decision-rejected';
                elseif($ap->decision === 'conditional') $decisionClass = 'decision-conditional';
              @endphp
              <span class="decision-badge {{ $decisionClass }}">
                @if($ap->decision === 'approved')
                  ✓ {{ __('assets.disposal.a_'.$ap->decision) }}
                @elseif($ap->decision === 'rejected')
                  ✗ {{ __('assets.disposal.a_'.$ap->decision) }}
                @elseif($ap->decision === 'pending')
                  ⏳ {{ __('assets.disposal.a_'.$ap->decision) }}
                @else
                  {{ __('assets.disposal.a_'.$ap->decision) }}
                @endif
              </span>
            </td>
            <td class="approver-cell">
              {{ $ap->approverUser?->name ?? ($ap->decision !== 'pending' ? '-' : '') }}
            </td>
            <td class="date-cell">
              @if($ap->decided_at)
                <div>{{ \Carbon\Carbon::parse($ap->decided_at)->format('Y-m-d') }}</div>
                <div style="font-size: 11px; color: #adb5bd">{{ \Carbon\Carbon::parse($ap->decided_at)->format('H:i') }}</div>
              @else
                <span class="empty-value">-</span>
              @endif
            </td>
            <td class="notes-cell">
              {{ $ap->decision_notes ?: '-' }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <!-- رسالة عند عدم وجود موافقات -->
    <div class="no-approvals">
      <div class="no-approvals-icon">📭</div>
      <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px">
        {{ __('لا توجد موافقات مسجلة') }}
      </div>
      <div style="font-size: 14px">
        {{ __('لم يتم تسجيل أي قرارات للجنة بعد') }}
      </div>
    </div>
  @endif

  <!-- زر الطباعة -->
  <div class="footer-section no-print">
    <button class="print-button" onclick="window.print()">
      🖨️ {{ __('طباعة المحضر') }}
    </button>
  </div>
</div>
</body>
</html>
