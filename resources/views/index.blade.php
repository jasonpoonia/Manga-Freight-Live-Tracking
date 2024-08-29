@extends('layouts.app')
@section('content')

    <!--Page Title
    <section class="page-title" style="background-image:url({{asset('images/cover.jpg')}});">
        <div class="auto-container">
            <h2>Track & Trace</h2>
            <div class="separater"></div>
        </div>
    </section>
-->
    <!--Breadcrumb
    <div class="breadcrumb-outer">
        <div class="auto-container">
            <ul class="bread-crumb text-center">
                <li><a href="https://tsbliving.co.nz">Home</a> <span>/</span></li>
                <li>Track & Trace</li>
            </ul>
        </div>
    </div>
    -->
    <!--End Page Title-->

    <!--Sidebar Page Container-->
    <div class="sidebar-page-container">
        <div class="auto-container" style="padding:20px;">
            <div class="row clearfix">

                <!--Content Side-->
                <div class="content-side" style="width: 100%;">
                    <div class="track-section">
                        <!-- Sec Title Two 
                            <div class="logo">
                                <a href="{{url('/')}}">
                                    <img src="{{asset('images/icons/Asset_1Red_400x.avif')}}" alt="" title="" width="300px">
                                </a>
                            </div>
                            -->
                        <!--<div class="sec-title-two sec-title">-->
                        <!--    <h2>Track & <span>Trace Shipment</span></h2>-->
                        <!--    <div class="separater"></div>-->
                        <!--</div>-->
                        <div id="loadingGif" style="display:none">
                            <img src="https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif" alt="">
                        </div>

                        <!-- Track Form -->
                        <div class="track-form-two">
                            <form id="trackForm">
                                <div class="form-group">
                                    <label>Enter Tracking Code Here</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" id="salesOrderNumber"
                                       placeholder="Enter your tracking code here e.g SOXXXXX"
                                       value="{{request('order','')}}"
                                    >
                                    <button type="submit" class="theme-btn submit-btn">Track Your Shipment</button>
                                </div>
                            </form>
                        </div>

                        <!-- Dynamic tracking info will be inserted here -->
                        <div id="trackingInfoContainer" class="tracking-info-detail"></div>
                        
                        <!-- External Tracking Container -->
                        <div id="externalTrackingContainer"></div>
                    </div>
                </div>


            </div>
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
