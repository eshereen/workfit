@extends('layouts.app')
@section('content')
  <!-- Hero -->


  <!-- Content -->
  <div class="max-w-5xl lg:max-w-7xl mx-auto px-4 my-2">
    <!-- Full Width Product Grid -->
    <main>
      @livewire('product-index', [
        'category' => $category,
        'categorySlug' => $categorySlug,
        'subcategory' => $subcategory->id,
        'subcategorySlug' => $subcategorySlug
      ])
    </main>
  </div>

@endsection
