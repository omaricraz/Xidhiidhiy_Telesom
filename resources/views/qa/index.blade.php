@extends('layouts.master')

@section('title', 'Q&A - Suaalo')

@section('content')

@php
    // Define team colors mapping - Yellow, Green, Purple
    $teamColors = [
        'Dev' => [
            'badge' => 'bg-warning', 
            'text' => 'text-dark', 
            'borderColor' => '#ffc107' // Yellow
        ],
        'DevOps' => [
            'badge' => 'bg-success', 
            'text' => 'text-white', 
            'borderColor' => '#198754' // Green
        ],
        'Global' => [
            'badge' => 'bg-secondary', 
            'text' => 'text-white', 
            'borderColor' => '#6f42c1' // Purple
        ],
    ];
    
    // Function to get team color
    $getTeamColor = function($teamName) use ($teamColors) {
        // Default to purple for unknown teams
        return $teamColors[$teamName] ?? [
            'badge' => 'bg-primary', 
            'text' => 'text-white', 
            'borderColor' => '#6f42c1' // Purple
        ];
    };
@endphp

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
            <div class="d-flex justify-content-between align-items-center mb-3">
              @can('create', App\Models\Question::class)
              <a href="{{ route('qa.create') }}" class="btn btn-primary">Add Question</a>
              @else
              <div></div>
              @endcan
              @if(Auth::user()->isManager() && $allTeams->count() > 0)
              <div>
                <form method="GET" action="{{ route('qa.index') }}" class="d-inline">
                  <select name="team" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Questions</option>
                    <option value="global" {{ $selectedTeam === 'global' ? 'selected' : '' }}>Global Questions</option>
                    @foreach($allTeams as $team)
                      <option value="{{ $team->id }}" {{ $selectedTeam == $team->id ? 'selected' : '' }}>{{ $team->name }} Team</option>
                    @endforeach
                  </select>
                </form>
              </div>
              @endif
            </div>
            <div class="card">
              <div class="card-header">
                <h5>Frequently Asked Questions</h5>
              </div>
              <div class="card-body">
                @if($questions->count() > 0)
                  @php
                    // Order categories: Global (General) first, then Dev, then DevOps, then others
                    $categoryOrder = ['Global', 'Dev', 'DevOps'];
                    $orderedCategories = collect($categoryOrder)
                      ->filter(fn($cat) => $questionsByTeam->has($cat))
                      ->merge($questionsByTeam->keys()->diff($categoryOrder))
                      ->values();
                  @endphp
                  
                  {{-- Main Accordion for Categories --}}
                  <div class="accordion" id="categoryAccordion">
                    @foreach($orderedCategories as $categoryIndex => $teamName)
                      @php
                        $teamQuestions = $questionsByTeam->get($teamName);
                        $teamColor = $getTeamColor($teamName);
                        $categoryId = 'category' . ($categoryIndex + 1);
                        $isFirst = $categoryIndex === 0;
                      @endphp
                      
                      <div class="accordion-item" style="border-left: 4px solid {{ $teamColor['borderColor'] }};">
                        <h2 class="accordion-header" id="heading{{ $categoryId }}">
                          <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $categoryId }}" aria-expanded="{{ $isFirst ? 'true' : 'false' }}" aria-controls="collapse{{ $categoryId }}">
                            <span class="badge {{ $teamColor['badge'] }} {{ $teamColor['text'] }} me-3">
                              {{ $teamName === 'Global' ? 'General' : $teamName }}
                            </span>
                            <span class="fw-bold">{{ $teamName === 'Global' ? 'General Questions' : $teamName . ' Team Questions' }}</span>
                            <span class="badge bg-light text-dark ms-auto">{{ $teamQuestions->count() }} {{ $teamQuestions->count() === 1 ? 'question' : 'questions' }}</span>
                          </button>
                        </h2>
                        <div id="collapse{{ $categoryId }}" class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}" aria-labelledby="heading{{ $categoryId }}" data-bs-parent="#categoryAccordion">
                          <div class="accordion-body p-0">
                            {{-- Nested Accordion for Questions within this Category --}}
                            <div class="accordion accordion-flush" id="questionAccordion{{ $categoryId }}">
                              @foreach($teamQuestions as $qIndex => $question)
                                <div class="accordion-item" style="border-left: 3px solid {{ $teamColor['borderColor'] }};">
                                  <h2 class="accordion-header" id="qHeading{{ $question->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qCollapse{{ $question->id }}" aria-expanded="false" aria-controls="qCollapse{{ $question->id }}">
                                      <span class="me-2">{{ $question->question }}</span>
                                    </button>
                                  </h2>
                                  <div id="qCollapse{{ $question->id }}" class="accordion-collapse collapse" aria-labelledby="qHeading{{ $question->id }}" data-bs-parent="#questionAccordion{{ $categoryId }}">
                                    <div class="accordion-body">
                                      <div class="mb-3">
                                        {!! nl2br(e($question->answer ?? 'Answer not available.')) !!}
                                      </div>
                                      <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div>
                                          <small class="text-muted">
                                            <i class="ti ti-user me-1"></i>Created by: {{ $question->creator->name }}
                                          </small>
                                          <small class="text-muted ms-2">
                                            <i class="ti ti-calendar me-1"></i>{{ $question->created_at->format('M d, Y') }}
                                          </small>
                                        </div>
                                        <div>
                                          @can('update', $question)
                                          <a href="{{ route('qa.edit', $question) }}" class="btn btn-sm btn-primary me-2">
                                            <i class="ti ti-edit me-1"></i>Edit
                                          </a>
                                          @endcan
                                          @can('delete', $question)
                                          <form action="{{ route('qa.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                              <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                          </form>
                                          @endcan
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-5">
                    <p class="text-muted">No questions available at this time.</p>
                    @can('create', App\Models\Question::class)
                    <a href="{{ route('qa.create') }}" class="btn btn-primary mt-3">Create First Question</a>
                    @endcan
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
@endsection

