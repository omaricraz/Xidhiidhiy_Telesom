@extends('layouts.master')

@section('title', 'Tasks')

@section('css')
@if(Auth::user()->isManager() || Auth::user()->isTeamLead())
<link rel="stylesheet" href="/build/css/plugins/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="/build/css/plugins/responsive.bootstrap5.min.css" />
@endif
<style>
  /* Task title styling for light mode */
  [data-pc-theme="light"] .task-title {
    color: #000000 !important;
    font-weight: bold !important;
  }
  
  /* Task title in table view for light mode */
  [data-pc-theme="light"] .task-title-table {
    color: #000000 !important;
    font-weight: bold !important;
  }
  
  /* Task title styling for dark mode - white color */
  [data-pc-theme="dark"] .task-title {
    color: #ffffff !important;
    font-weight: bold !important;
  }
  
  /* Task title in table view for dark mode - white color */
  [data-pc-theme="dark"] .task-title-table {
    color: #ffffff !important;
    font-weight: bold !important;
  }
  
  /* Tasks header styling */
  .tasks-header {
    text-align: center;
    font-size: 2rem;
    text-decoration: underline;
    margin-bottom: 1rem;
  }
</style>
@endsection

@section('content')

@if(Auth::user()->isManager() || Auth::user()->isTeamLead())
<x-breadcrumb item="Tasks" active="List"/>
@endif

        <!-- [ Main Content ] start -->
        <div class="row">
          @if(Auth::user()->isManager() || Auth::user()->isTeamLead())
            {{-- Managers and Team Leads see only the table view --}}
            <div class="col-12">
              <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Tasks</h5>
                  <a href="{{ route('tasks.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="ti ti-plus f-18"></i> Add Task
                  </a>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover" id="pc-dt-simple">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Title</th>
                          <th>Priority</th>
                          <th>Status</th>
                          <th>Creator</th>
                          <th>Assignee</th>
                          <th class="text-center">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($tasks as $task)
                        <tr>
                          <td>{{ $task->id }}</td>
                          <td><span class="task-title-table">{{ $task->title }}</span></td>
                          <td>
                            <span class="badge bg-light-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'success') }}">
                              {{ $task->priority }}
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-light-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In_Progress' ? 'primary' : 'secondary') }}">
                              {{ $task->status }}
                            </span>
                          </td>
                          <td>{{ $task->creator->name }}</td>
                          <td>{{ $task->assignee->name ?? 'Unassigned' }}</td>
                          <td class="text-center">
                            <ul class="list-inline me-auto mb-0">
                              <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View">
                                <a href="{{ route('tasks.show', $task) }}" class="avtar avtar-xs btn-link-secondary btn-pc-default">
                                  <i class="ti ti-eye f-18"></i>
                                </a>
                              </li>
                              @can('update', $task)
                              <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                                <a href="{{ route('tasks.edit', $task) }}" class="avtar avtar-xs btn-link-success btn-pc-default">
                                  <i class="ti ti-edit-circle f-18"></i>
                                </a>
                              </li>
                              @endcan
                              @can('delete', $task)
                              <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="avtar avtar-xs btn-link-danger btn-pc-default border-0 bg-transparent p-0">
                                    <i class="ti ti-trash f-18"></i>
                                  </button>
                                </form>
                              </li>
                              @endcan
                            </ul>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-3">
                    {{ $tasks->links() }}
                  </div>
                </div>
              </div>
            </div>
          @elseif(Auth::user()->isEmployee() || Auth::user()->isIntern())
            {{-- Normal users (Employee and Intern) ONLY see "My Task" widget --}}
            <div class="col-12">
              <div class="card">
                <div class="card-body pb-0">
                  <h3 class="tasks-header mb-1">Tasks</h3>
                </div>
                <ul class="list-group list-group-flush border-top-0">
                  @if(isset($userTasks) && $userTasks->count() > 0)
                    @foreach($userTasks as $task)
                    <li class="list-group-item">
                      <div class="d-flex align-items-start">
                        <div class="flex-grow-1 me-2">
                          <h6 class="mb-0" style="font-size: 0.95rem;">
                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none task-title">{{ $task->title }}</a>
                          </h6>
                          <p class="my-1">
                            <i class="ti ti-{{ $task->status === 'Completed' ? 'check' : ($task->status === 'In_Progress' ? 'clock' : 'archive') }}"></i> 
                            {{ $task->status }}
                            @if($task->assignee)
                              - Assigned by {{ $task->creator->name }}
                            @endif
                          </p>
                          <span class="badge bg-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'success') }} rounded-pill">
                            {{ $task->priority }}
                          </span>
                        </div>
                        <div class="flex-shrink-0">
                          @if($task->status === 'Completed')
                            <a href="#" class="avtar avtar-s btn-link-secondary">
                              <i class="ti ti-circle-check text-success f-18"></i>
                            </a>
                          @elseif($task->assignee_id === Auth::id() && $task->status === 'Pending')
                            <form action="{{ route('tasks.accept', $task) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="avtar avtar-s btn-link-secondary border-0 bg-transparent p-0" data-bs-toggle="tooltip" title="Accept Task">
                                <i class="ti ti-check f-18"></i>
                              </button>
                            </form>
                          @elseif($task->assignee_id === Auth::id() && $task->status !== 'Completed')
                            <form action="{{ route('tasks.completed', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this task as completed?');">
                              @csrf
                              <button type="submit" class="avtar avtar-s btn-link-secondary border-0 bg-transparent p-0" data-bs-toggle="tooltip" title="Mark as Completed">
                                <i class="ti ti-checkbox f-18"></i>
                              </button>
                            </form>
                          @else
                            <a href="{{ route('tasks.show', $task) }}" class="avtar avtar-s btn-link-secondary">
                              <i class="ti ti-circle-check f-18"></i>
                            </a>
                          @endif
                        </div>
                      </div>
                    </li>
                    @endforeach
                  @else
                    <li class="list-group-item">
                      <div class="text-center text-muted py-3">
                        <p class="mb-0">No tasks assigned to you</p>
                      </div>
                    </li>
                  @endif
                </ul>
              </div>
            </div>
          @endif
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    @if(Auth::user()->isManager() || Auth::user()->isTeamLead())
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="/build/js/plugins/dataTables.min.js"></script>
    <script src="/build/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script src="/build/js/plugins/dataTables.responsive.min.js"></script>
    <script src="/build/js/plugins/responsive.bootstrap5.min.js"></script>
    <script>
      $('#pc-dt-simple').DataTable({
        responsive: true,
        order: [[0, 'desc']]  // Sort by first column (ID) in descending order
      });
    </script>
    @endif
    <!-- [Page Specific JS] end -->
@endsection
