@extends('layouts.master')

@section('title', 'إضافة نوع جديد')

@section('content')
<div class="container mt-4">
    <h2>إضافة نوع جديد</h2>

    <form action="{{ route('answer_types.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>الاسم (إنجليزي)</label>
            <input type="text" class="form-control" name="name_en" required>
        </div>

        <div class="form-group">
            <label>الاسم (عربي)</label>
            <input type="text" class="form-control" name="name_ar" required>
        </div>

        <div class="form-group">
            <label>النوع</label>
            <select class="form-control" name="type" required>
                <option value="value">Value</option>
                <option value="list">List</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-2">حفظ</button>
        <a href="{{ route('answer_types.index') }}" class="btn btn-secondary mt-2">إلغاء</a>
    </form>
</div>
@endsection
