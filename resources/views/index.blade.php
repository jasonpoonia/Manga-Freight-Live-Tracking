@extends('layouts.app')
@section('content')
<div class="tracking-container">
    <div class="tracking-wrapper">
        <!-- Search Box Section -->
        <div class="tracking-search">
            <form id="trackForm">
                <div class="search-input-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" 
                           id="salesOrderNumber"
                           placeholder="Enter your tracking number (e.g., TSB123456, SO123456)"
                           class="search-input"
                           value="{{request('order','')}}"
                    >
                    <button type="submit" class="search-button">
                        <svg class="package-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"></path>
                            <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"></path>
                            <path d="M12 3v6"></path>
                        </svg>
                        Track
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingGif" class="loading-spinner">
            <div class="spinner"></div>
        </div>

        <!-- Tracking Results -->
        <div id="trackingInfoContainer" class="tracking-results"></div>

        <!-- External Tracking Container -->
        <div id="externalTrackingContainer" class="tracking-results"></div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{asset('js/track.js')}}?v=1.2"></script>
    <script type="text/javascript" src="//www.17track.net/externalcall.js" defer></script>
    
    @if(request()->has('order'))
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                await fetchTrackingInformation("{{request()->get('order')}}");
            });
        </script>
    @endif
@endpush