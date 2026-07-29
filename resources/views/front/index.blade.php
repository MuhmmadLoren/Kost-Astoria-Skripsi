@extends('layouts.front.app')
@section('description')
@endsection
@section('title')
Selamat Datang di Kost Astoria
@endsection


@section('content')
@include('front.banner')
@if ($promo->count() > 0)
@include('front.sliderCard')
@endif
@include('front.cardContent')
{{-- @include('front.byKota') --}}

@endsection