@extends('layouts.app')
@section('content')
  <!-- Hero -->
  <section class="relative">
    <img src="https://images.unsplash.com/photo-1637666532931-b835a227b955?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hero" class="object-cover w-full h-64 md:h-96">
    <div class="flex absolute inset-0 flex-col justify-center items-center text-white bg-black/50">
      <h1 class="text-4xl font-bold capitalize md:text-6xl">
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
    <div class="px-4 py-10 mx-auto my-8 max-w-7xl">
      <!-- Mobile Layout: Filters on top -->
      <div class="block space-y-6 md:hidden">
        <!-- Mobile Filters -->
        <aside class="space-y-6">
          @livewire('category-filters', ['categorySlug' => $categorySlug])
        </aside>

        <!-- Mobile Product Grid -->
        <main>
          @livewire('category-products', ['categorySlug' => $categorySlug])
        </main>
      </div>

      <!-- Desktop Layout: Sidebar and products beside each other -->
      <div class="hidden gap-8 md:grid md:grid-cols-4">
        <!-- Desktop Sidebar Filters -->
        <aside class="space-y-6">
          @livewire('category-filters', ['categorySlug' => $categorySlug])
        </aside>

        <!-- Desktop Product Grid -->
        <main class="md:col-span-3">
          @livewire('category-products', ['categorySlug' => $categorySlug])
        </main>
      </div>
    </div>
  @else
    <!-- Show all categories in grid -->
    <div class="px-4 py-10 mx-auto my-8 max-w-7xl">
      @livewire('categories-grid')
    </div>
  @endif

@endsection
