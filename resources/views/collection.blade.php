@extends('layouts.app')
@section('content')
  <!-- Hero -->


  <!-- Content -->
  <div class="px-4 py-4 mx-auto my-8 max-w-5xl lg:max-w-7xl">
    <!-- Mobile Layout: Filters on top -->
    <div class="block space-y-6 md:hidden">
      <!-- Mobile Filters -->
      <aside class="space-y-6">
        @livewire('collection-filters', ['collectionSlug' => $collectionSlug])

      <!-- Mobile Product Grid -->
      <main>
        @livewire('collection-products', ['collectionSlug' => $collectionSlug])
      </main>
    </div>

    <!-- Desktop Layout: Sidebar and products beside each other -->
    <div class="hidden gap-8 md:grid md:grid-cols-4">
      <!-- Desktop Sidebar Filters -->
      <aside class="space-y-6">
        @livewire('collection-filters', ['collectionSlug' => $collectionSlug])
      </aside>

      <!-- Desktop Product Grid -->
      <main class="md:col-span-3">
        @livewire('collection-products', ['collectionSlug' => $collectionSlug])
      </main>
    </div>
  </div>

@endsection
