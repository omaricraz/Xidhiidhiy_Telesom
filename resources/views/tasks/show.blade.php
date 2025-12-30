@extends('layouts.master')

@section('title', 'Task Details')

@section('content')

<x-breadcrumb item="Tasks" active="Details"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>Task Details</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Title:</strong> {{ $task->title }}</p>
                    <p><strong>Description:</strong> {{ $task->description ?? 'N/A' }}</p>
                    <p><strong>Priority:</strong> 
                      <span class="badge bg-light-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'success') }}">
                        {{ $task->priority }}
                      </span>
                    </p>
                    <p><strong>Status:</strong> 
                      <span class="badge bg-light-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In_Progress' ? 'primary' : 'secondary') }}">
                        {{ $task->status }}
                      </span>
                    </p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Creator:</strong> {{ $task->creator->name }}</p>
                    <p><strong>Assignee:</strong> {{ $task->assignee->name ?? 'Unassigned' }}</p>
                    <p><strong>Private:</strong> 
                      @if($task->is_private)
                        <span class="badge bg-light-danger">Yes</span>
                      @else
                        <span class="badge bg-light-success">No</span>
                      @endif
                    </p>
                    <p><strong>Created:</strong> {{ $task->created_at->format('Y-m-d H:i') }}</p>
                  </div>
                </div>
                <div class="text-end mt-3">
                  <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Back</a>
                  @can('update', $task)
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">Edit</a>
                  @endcan
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
@endsection

