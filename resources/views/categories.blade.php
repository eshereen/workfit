@extends('layouts.app')
@section('content')
  <!-- Hero -->
  <section class="relative">
    <img src="https://images.unsplash.com/photo-1637666532931-b835a227b955?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hero" class="w-full h-64 md:h-96 object-cover">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white bg-black/50">
      <h1 class="text-4xl md:text-6xl font-bold capitalize">
        @if($category)
          {{ $category->name }}
        @else
          Categories
        @endif
      </h1>
      <p class="mt-2 text-lg">Technical solutions for warm-weather wear</p>
    </div>
  </section>

  <!-- Content -->
  @if($category)
    <!-- Show products for specific category -->
    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 my-8">
      <!-- Sidebar Filters -->
      <aside class="space-y-6">
        @livewire('category-filters', ['categorySlug' => $categorySlug])
      </aside>

      <!-- Product Grid -->
      <main class="md:col-span-3">
        @livewire('category-products', ['categorySlug' => $categorySlug])
      </main>
    </div>
  @else
    <!-- Show all categories in grid -->
    <div class="max-w-7xl mx-auto px-4 py-10 my-8">
      @livewire('categories-grid')
    </div>
  @endif

@endsection
