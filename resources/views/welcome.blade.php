@extends('layouts.app')

@section('title', 'Laravel Store - Главная')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Hero Section -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Добро пожаловать в Laravel Store!</h1>
        <p class="text-xl text-gray-600 mb-8">Лучший интернет-магазин с широким ассортиментом товаров</p>
        <a href="{{ route('catalog') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-lg hover:bg-indigo-700 transition font-semibold text-lg">
            <i class="fas fa-shopping-bag mr-3"></i>Перейти к покупкам
        </a>
    </div>

    <!-- Features -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <i class="fas fa-shipping-fast text-indigo-600 text-3xl mb-4"></i>
            <h3 class="font-semibold text-gray-800 mb-2">Бесплатная доставка</h3>
            <p class="text-gray-600">При заказе от 3000 рублей</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <i class="fas fa-shield-alt text-indigo-600 text-3xl mb-4"></i>
            <h3 class="font-semibold text-gray-800 mb-2">Гарантия качества</h3>
            <p class="text-gray-600">30 дней на возврат товара</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <i class="fas fa-headset text-indigo-600 text-3xl mb-4"></i>
            <h3 class="font-semibold text-gray-800 mb-2">Поддержка 24/7</h3>
            <p class="text-gray-600">Всегда готовы помочь</p>
        </div>
    </div>
</div>
@endsection