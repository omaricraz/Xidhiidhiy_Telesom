@extends('layouts.master')

@section('title', 'Q&A - Suaalo')

@section('content')

<x-breadcrumb item="Q&A" active="Suaalo"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header">
                <h5>Frequently Asked Questions</h5>
              </div>
              <div class="card-body">
                @if($faqs->count() > 0)
                  <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                          {{ $faq->question ?? 'Question ' . ($index + 1) }}
                        </button>
                      </h2>
                      <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                          {{ $faq->answer ?? 'Answer not available.' }}
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-5">
                    <p class="text-muted">No FAQs available at this time.</p>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
@endsection

