@extends('layouts.master')

@section('title', 'Q&A - Suaalo')

@section('content')

<x-breadcrumb item="Q&A" active="Suaalo"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-12">
            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            @can('create', App\Models\Question::class)
            <div class="text-end mb-3">
              <a href="{{ route('qa.create') }}" class="btn btn-primary">Add Question</a>
            </div>
            @endcan
            <div class="card">
              <div class="card-header">
                <h5>Frequently Asked Questions</h5>
              </div>
              <div class="card-body">
                @if($questions->count() > 0)
                  <div class="accordion" id="faqAccordion">
                    @foreach($questions as $index => $question)
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading{{ $question->id }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $question->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $question->id }}">
                          {{ $question->question }}
                        </button>
                      </h2>
                      <div id="collapse{{ $question->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $question->id }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                          <div class="mb-3">
                            {!! nl2br(e($question->answer ?? 'Answer not available.')) !!}
                          </div>
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              @if($question->team)
                                <span class="badge bg-light-info me-2">Team: {{ $question->team->name }}</span>
                              @else
                                <span class="badge bg-light-secondary me-2">Global</span>
                              @endif
                              <small class="text-muted">Created by: {{ $question->creator->name }}</small>
                            </div>
                            <div>
                              @can('update', $question)
                              <a href="{{ route('qa.edit', $question) }}" class="btn btn-sm btn-primary me-2">Edit</a>
                              @endcan
                              @can('delete', $question)
                              <form action="{{ route('qa.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                              </form>
                              @endcan
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-5">
                    <p class="text-muted">No questions available at this time.</p>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
@endsection

