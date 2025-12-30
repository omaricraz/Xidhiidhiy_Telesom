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
            <div class="card table-card">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover" id="pc-dt-simple">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Team</th>
                        <th>Status</th>
                        <th>Tech Stack</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($users as $user)
                      <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                          <div class="row">
                            <div class="col-auto">
                              <span class="avtar avtar-s rounded-circle bg-light-primary">
                                {{ $user->status_emoji ?? '👤' }}
                              </span>
                            </div>
                            <div class="col">
                              <h6 class="mb-0">{{ $user->full_name ?? $user->name }}</h6>
                              <p class="text-muted f-12 mb-0">{{ $user->email }}</p>
                            </div>
                          </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-light-{{ $user->role === 'Manager' ? 'danger' : ($user->role === 'Team_Lead' ? 'warning' : 'primary') }}">{{ $user->role }}</span></td>
                        <td>{{ $user->team->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-light-success rounded-pill f-12">Active</span></td>
                        <td>
                          @if($user->tech_stack)
                            @foreach(explode(',', $user->tech_stack) as $tech)
                              <span class="badge bg-light-secondary me-1">{{ trim($tech) }}</span>
                            @endforeach
                          @else
                            N/A
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
      import { DataTable } from '/build/js/plugins/module.js';
      window.dt = new DataTable('#pc-dt-simple');
    </script>
@endsection

