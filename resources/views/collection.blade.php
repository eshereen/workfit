@extends('layouts.app')
@section('content')
  <!-- Hero -->
  <section class="relative">
    <img src="https://via.placeholder.com/1600x400" alt="Hero" class="w-full h-64 md:h-96 object-cover">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white bg-black bg-opacity-40">
      <h1 class="text-4xl md:text-6xl font-bold capitalize">{{ $collection->name ?? $collectionSlug }}</h1>
      <p class="mt-2 text-lg">Technical solutions for warm-weather wear</p>
    </div>
  </section>

  <!-- Content -->
  <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 my-8">

    <!-- Sidebar Filters -->
    <aside class="space-y-6">
      @livewire('collection-filters', ['collectionSlug' => $collectionSlug])
    </aside>

    <!-- Product Grid -->
    <main class="md:col-span-3 ">
      <!-- Products -->
      @livewire('collection-products', ['collectionSlug' => $collectionSlug])
    </main>
  </div>

@endsection
