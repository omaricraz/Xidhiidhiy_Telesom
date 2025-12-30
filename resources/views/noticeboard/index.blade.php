@extends('layouts.master')

@section('title', 'Noticeboard')

@section('content')

<x-breadcrumb item="Noticeboard" active="Announcements"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header">
                <h5>Noticeboard</h5>
              </div>
              <div class="card-body">
                <div class="chat-wrapper">
                  <div class="card">
                    <div class="card-body">
                      <div class="chat-messages">
                        @if($notices->count() > 0)
                          @foreach($notices as $notice)
                          <div class="chat-message mb-3 p-3 border rounded">
                            <div class="d-flex align-items-start">
                              <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $notice->title ?? 'Announcement' }}</h6>
                                <p class="mb-0 text-muted">{{ $notice->content ?? 'No content available.' }}</p>
                                <small class="text-muted">{{ $notice->created_at ?? now() }}</small>
                              </div>
                            </div>
                          </div>
                          @endforeach
                        @else
                          <div class="text-center py-5">
                            <p class="text-muted">No announcements at this time.</p>
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

