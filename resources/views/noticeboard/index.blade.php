@extends('layouts.master')

@section('title', 'Noticeboard')

@section('content')

<x-breadcrumb item="Noticeboard" active="Announcements"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Noticeboard</h5>
                @can('create', App\Models\Notice::class)
                  <a href="{{ route('noticeboard.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather icon-plus"></i> Create Notice
                  </a>
                @endcan
              </div>
              <div class="card-body">
                @if(session('success'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                @endif
                <div class="chat-wrapper">
                  <div class="card">
                    <div class="card-body">
                      <div class="chat-messages">
                        @if($notices->count() > 0)
                          @foreach($notices as $notice)
                          <div class="chat-message mb-3 p-3 border rounded">
                            <div class="d-flex align-items-start">
                              <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                  <h6 class="mb-1">{{ $notice->title }}</h6>
                                  @canany(['update', 'delete'], $notice)
                                    <div class="btn-group btn-group-sm" role="group">
                                      @can('update', $notice)
                                        <a href="{{ route('noticeboard.edit', $notice) }}" class="btn btn-outline-primary btn-sm">
                                          <i class="feather icon-edit"></i> Edit
                                        </a>
                                      @endcan
                                      @can('delete', $notice)
                                        <form action="{{ route('noticeboard.destroy', $notice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="feather icon-trash-2"></i> Delete
                                          </button>
                                        </form>
                                      @endcan
                                    </div>
                                  @endcanany
                                </div>
                                <p class="mb-2 text-muted">{{ $notice->content }}</p>
                                <small class="text-muted">
                                  <i class="feather icon-user"></i> {{ $notice->creator->name ?? 'Unknown' }} 
                                  <span class="mx-2">|</span>
                                  <i class="feather icon-clock"></i> {{ $notice->created_at->format('M d, Y h:i A') }}
                                </small>
                              </div>
                            </div>
                          </div>
                          @endforeach
                          
                          <!-- Pagination -->
                          <div class="mt-4">
                            {{ $notices->links() }}
                          </div>
                        @else
                          <div class="text-center py-5">
                            <p class="text-muted">No announcements at this time.</p>
                            @can('create', App\Models\Notice::class)
                              <a href="{{ route('noticeboard.create') }}" class="btn btn-primary mt-2">
                                <i class="feather icon-plus"></i> Create First Notice
                              </a>
                            @endcan
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
@endsection

