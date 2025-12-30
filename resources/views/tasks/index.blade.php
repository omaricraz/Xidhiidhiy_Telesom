@extends('layouts.master')

@section('title', 'Tasks')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="/build/css/plugins/responsive.bootstrap5.min.css" />
@endsection

@section('content')

<x-breadcrumb item="Tasks" active="List"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card table-card">
              <div class="card-body">
                <div class="text-end p-4 pb-sm-2">
                  <a href="{{ route('tasks.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="ti ti-plus f-18"></i> Add Task
                  </a>
                </div>
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
                        <th>Private</th>
                        <th class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($tasks as $task)
                      <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->title }}</td>
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
                        <td>
                          @if($task->is_private)
                            <span class="badge bg-light-danger">Private</span>
                          @else
                            <span class="badge bg-light-success">Public</span>
                          @endif
                        </td>
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
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="/build/js/plugins/dataTables.min.js"></script>
    <script src="/build/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script src="/build/js/plugins/dataTables.responsive.min.js"></script>
    <script src="/build/js/plugins/responsive.bootstrap5.min.js"></script>
    <script>
      $('#pc-dt-simple').DataTable({
        responsive: true
      });
    </script>
    <!-- [Page Specific JS] end -->
@endsection

