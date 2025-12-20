@extends('layouts.app')
@section('content')
  <!-- Hero -->

  <!-- Content -->
  @if($category)
    <!-- Show products for specific category -->
    <div class="px-4  mx-auto my-4 max-w-5xl lg:max-w-7xl">
      <!-- Full Width Product Grid -->
      <main>
        @livewire('product-index', ['categorySlug' => $categorySlug, 'category' => $category])
      </main>
    </div>
  @else
    <!-- Show all categories in grid -->
    <div class="px-4 py-10 mx-auto my-8 max-w-7xl">
      @livewire('categories-grid')
    </div>
  @endif

@endsection
