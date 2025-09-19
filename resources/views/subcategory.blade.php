@extends('layouts.app')
@section('content')
  <!-- Hero -->
  <section class="relative">
    <img src="https://images.unsplash.com/photo-1637666532931-b835a227b955?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hero" class="w-full h-64 md:h-96 object-cover">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white bg-black/50">
      <h1 class="text-4xl md:text-6xl font-bold capitalize">
        {{ $subcategory->name }}
      </h1>
      <p class="mt-2 text-lg">{{ $category->name }} - {{ $subcategory->name }}</p>
      <!-- Breadcrumb -->
      <nav class="mt-4 text-sm">
        <ol class="flex items-center space-x-2">
          <li><a href="{{ route('home') }}" class="hover:text-red-300">Home</a></li>
          <li><span class="text-gray-300">/</span></li>
          <li><a href="{{ route('categories.all') }}" class="hover:text-red-300">Categories</a></li>
          <li><span class="text-gray-300">/</span></li>
          <li><a href="{{ route('categories.index', $category->slug) }}" class="hover:text-red-300">{{ $category->name }}</a></li>
          <li><span class="text-gray-300">/</span></li>
          <li class="text-gray-300">{{ $subcategory->name }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!-- Content -->
  <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 my-8">
    <!-- Sidebar Filters -->
    <aside class="space-y-6">
      @livewire('category-filters', ['categorySlug' => $categorySlug, 'subcategorySlug' => $subcategorySlug])
    </aside>

    <!-- Product Grid -->
    <main class="md:col-span-3">
      @livewire('category-products', ['categorySlug' => $categorySlug, 'subcategorySlug' => $subcategorySlug])
    </main>
  </div>

@endsection
