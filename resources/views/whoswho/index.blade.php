@extends('layouts.master')

@section('title', 'Who\'s Who')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Who's Who" active="Team Members"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ sample-page ] start -->
          <div class="col-sm-12">
            @if($teamMembers->count() > 0)
            <!-- Team Members Section -->
            <div class="mb-4">
              <h5 class="mb-3">
                <span class="badge bg-light-primary me-2">{{ $teamMembers->count() }}</span>
                My Team Members
                @if($user->team)
                  <small class="text-muted">({{ $user->team->name }})</small>
                @endif
              </h5>
              <div class="row g-3">
                @foreach($teamMembers as $member)
                <div class="col-md-6 col-lg-4 col-xl-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                          @if($member->profile_image)
                            <img src="{{ $member->profile_image }}" alt="{{ $member->full_name ?? $member->name }}" class="avtar avtar-m rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                          @else
                            <span class="avtar avtar-m rounded-circle bg-light-primary">
                              {{ $member->status_emoji ?? '👤' }}
                            </span>
                          @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="card-title mb-0">{{ $member->full_name ?? $member->name }}</h6>
                          <p class="text-muted f-12 mb-0">{{ $member->email }}</p>
                        </div>
                      </div>
                      
                      <div class="mb-2">
                        <small class="text-muted d-block mb-1">Role</small>
                        <span class="badge bg-light-{{ $member->role === 'Manager' ? 'danger' : ($member->role === 'Team_Lead' ? 'warning' : 'primary') }}">
                          {{ $member->role }}
                        </span>
                      </div>
                      
                      <div class="mb-2">
                        <small class="text-muted d-block mb-1">Team</small>
                        <p class="mb-0 f-14">{{ $member->team->name ?? 'N/A' }}</p>
                      </div>
                      
                      <div class="mb-2">
                        <small class="text-muted d-block mb-1">Status</small>
                        <span class="badge bg-light-success rounded-pill f-12">Active</span>
                      </div>
                      
                      @if($member->tech_stack)
                      <div>
                        <small class="text-muted d-block mb-1">Tech Stack</small>
                        <div class="d-flex flex-wrap gap-1">
                          @foreach(explode(',', $member->tech_stack) as $tech)
                            <span class="badge bg-light-secondary">{{ trim($tech) }}</span>
                          @endforeach
                        </div>
                      </div>
                      @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                      <small class="text-muted">ID: #{{ $member->id }}</small>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endif

            @if($otherMembers->count() > 0)
            <!-- Other Team Members Section -->
            <div>
              <h5 class="mb-3">
                <span class="badge bg-light-secondary me-2">{{ $otherMembers->count() }}</span>
                Other Team Members
              </h5>
              <div class="row g-3">
                @foreach($otherMembers as $member)
                <div class="col-md-6 col-lg-4 col-xl-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                          @if($member->profile_image)
                            <img src="{{ $member->profile_image }}" alt="{{ $member->full_name ?? $member->name }}" class="avtar avtar-m rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                          @else
                            <span class="avtar avtar-m rounded-circle bg-light-primary">
                              {{ $member->status_emoji ?? '👤' }}
                            </span>
                          @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="card-title mb-0">{{ $member->full_name ?? $member->name }}</h6>
                          <p class="text-muted f-12 mb-0">{{ $member->email }}</p>
                        </div>
                      </div>
                      
                      <div class="mb-2">
                        <small class="text-muted d-block mb-1">Role</small>
                        <span class="badge bg-light-{{ $member->role === 'Manager' ? 'danger' : ($member->role === 'Team_Lead' ? 'warning' : 'primary') }}">
                          {{ $member->role }}
                        </span>
                      </div>
                      
                      <div class="mb-2">
                        <small class="text-muted d-block mb-1">Team</small>
                        <p class="mb-0 f-14">{{ $member->team->name ?? 'N/A' }}</p>
                      </div>
                      
                      <div class="mb-2">
                        <small class="text-muted d-block mb-1">Status</small>
                        <span class="badge bg-light-success rounded-pill f-12">Active</span>
                      </div>
                      
                      @if($member->tech_stack)
                      <div>
                        <small class="text-muted d-block mb-1">Tech Stack</small>
                        <div class="d-flex flex-wrap gap-1">
                          @foreach(explode(',', $member->tech_stack) as $tech)
                            <span class="badge bg-light-secondary">{{ trim($tech) }}</span>
                          @endforeach
                        </div>
                      </div>
                      @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                      <small class="text-muted">ID: #{{ $member->id }}</small>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endif
          </div>
          <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
@endsection

@section('scripts')
    <!-- No scripts needed for card groups -->
@endsection

