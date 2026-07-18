@extends('layouts.master')

@section('title', 'تعديل نوع إجابة')

@section('content')
<div class="container mt-4">
    <h2>تعديل نوع إجابة</h2>

    <form action="{{ route('answer_types.update', $answerType->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>الاسم (إنجليزي)</label>
            <input type="text" class="form-control" name="name_en" value="{{ old('name_en', $answerType->name_en) }}" required>
        </div>

        <div class="form-group">
            <label>الاسم (عربي)</label>
            <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar', $answerType->name_ar) }}" required>
        </div>

        <div class="form-group">
            <label>النوع</label>
            <select class="form-control" name="type" required id="type">
                <option value="value" {{ old('type', $answerType->type) == 'value' ? 'selected' : '' }}>Value</option>
                <option value="list" {{ old('type', $answerType->type) == 'list' ? 'selected' : '' }}>List</option>
            </select>
        </div>



        <button type="submit" class="btn btn-success mt-2">تحديث</button>
        <a href="{{ route('answer_types.index') }}" class="btn btn-secondary mt-2">إلغاء</a>
    </form>
</div>



@endsection
