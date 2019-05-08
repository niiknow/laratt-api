@extends('layouts.master')

@section('content')
<div id="myApp"></div>
@endsection

@push('footer_js')
<script src="{{ mix('js/myapp.js') }}"></script>
@endpush
