@extends('layouts.app')
@section('content')
  <!-- Hero -->
  <section class="relative">
    <img src="https://via.placeholder.com/1600x400" alt="Hero" class="w-full h-64 md:h-96 object-cover">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white bg-black bg-opacity-40">
      <h1 class="text-4xl md:text-6xl font-bold capitalize">Collections</h1>
      <p class="mt-2 text-lg">Discover our curated collections</p>
    </div>
  </section>

  <!-- Content -->
  <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 my-8">

    <!-- Sidebar Filters -->
    <aside class="space-y-6">
      @livewire('collection-filters', ['collectionSlug' => null])
    </aside>

    <!-- Collections Grid -->
    <main class="md:col-span-3">
      @livewire('collections-grid', ['collections' => $collections])
    </main>
  </div>

@endsection
