@extends('layouts.users')
@section('title', 'Create Promo Banner')
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Create Promo Banner</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card card-body mt-4">
                    <!-- Header Section -->
                    <div class="text-center mb-4">
                        <div class="icon-shape icon-lg bg-gradient-primary shadow mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 20px;">
                            <i class="fa fa-image text-white" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="font-weight-bolder text-gradient text-primary mb-2">Create Your Promotional Banner</h4>
                        <p class="text-sm opacity-8">Generate your personalized digital banner in minutes!</p>
                    </div>

                    <!-- Information Card -->
                    <div class="card bg-gradient-light mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-rounded me-2 text-primary">info</span>
                                <h6 class="mb-0 font-weight-bolder">About This Tool</h6>
                            </div>
                            <p class="text-sm opacity-8 mb-3">
                                <strong>Proud BTS 2025 Exhibitor!</strong> Add your organization name and other details 
                                to instantly create a share-ready creative for social media, email signatures, and websites.
                            </p>
                            <p class="text-sm opacity-8 mb-0">
                                <strong>Showcase your participation</strong> and be part of India's biggest tech showcase!
                            </p>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <div class="icon-shape icon-md bg-gradient-success shadow mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 15px;">
                                    <span class="material-symbols-rounded text-white">bolt</span>
                                </div>
                                <h6 class="font-weight-bolder mt-2">Quick & Easy</h6>
                                <p class="text-xs opacity-8">Generate in minutes</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <div class="icon-shape icon-md bg-gradient-info shadow mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 15px;">
                                    <span class="material-symbols-rounded text-white">edit</span>
                                </div>
                                <h6 class="font-weight-bolder mt-2">Customizable</h6>
                                <p class="text-xs opacity-8">Add your company details</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <div class="icon-shape icon-md bg-gradient-warning shadow mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 15px;">
                                    <span class="material-symbols-rounded text-white">share</span>
                                </div>
                                <h6 class="font-weight-bolder mt-2">Share Ready</h6>
                                <p class="text-xs opacity-8">Perfect for all platforms</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="text-center">
                        <a href="https://interlinxpartnering.com/bts/bts_forms/ebanner.php" 
                           target="_blank" 
                           class="btn btn-gradient-primary btn-lg shadow-lg px-6 mb-0">
                            <i class="fa fa-external-link me-2"></i>
                            Start Creating Your Banner
                        </a>
                        <p class="text-sm mt-3 opacity-6">
                            <i class="fa fa-info-circle me-1"></i>
                            Opens in a new window
                        </p>
                    </div>

                    <!-- Additional Info -->
                    <div class="alert alert-info mt-4 border-0">
                        <div class="d-flex align-items-start">
                            <span class="material-symbols-rounded me-2">lightbulb</span>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading font-weight-bolder">Tips for Best Results:</h6>
                                <ul class="mb-0 text-sm">
                                    <li>Have your company logo ready</li>
                                    <li>Prepare any key messages you want to highlight</li>
                                    <li>Use high-resolution images for better quality</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon-shape {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-md {
            width: 60px;
            height: 60px;
        }
        .icon-lg {
            width: 80px;
            height: 80px;
        }
        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: transform 0.2s;
        }
        .btn-gradient-primary:hover {
            transform: translateY(-2px);
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        .bg-gradient-light {
            background: linear-gradient(to bottom right, #f8f9fa, #e9ecef);
        }
    </style>
@endsection

