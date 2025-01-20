@extends('admin.layout')

@section('content')
    <div class="container">
        <h1 class="my-4">Order Details</h1>

        <div class="row">
            <!-- User Details -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">User Details</div>
                    <div class="card-body">
                        <p><strong>Name (AR):</strong> {{ $order->username_ar }}</p>
                        <p><strong>Name (EN):</strong> {{ $order->username_en }}</p>
                        <p><strong>Phone:</strong> {{ $order->user_phone_number }}</p>
                        <p><strong>Alternate Phone:</strong> {{ $order->delivery_number_two }}</p>
                        <p><strong>Gender:</strong> {{ $order->user_gender }}</p>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Address</div>
                    <div class="card-body">
                        <p><strong>Governorate:</strong> {{ $order->governorate }}</p>
                        <p><strong>Address:</strong> {{ $order->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Order Details</div>
                    <div class="card-body">
                        <p><strong>Book Type:</strong> {{ $order->bookType->name_ar ?? 'N/A' }}</p>
                        <p><strong>Design:</strong> {{ $order->bookDesign->name_ar ?? 'N/A' }}</p>
                        <p><strong>Pages Number:</strong> {{ $order->pages_number }}</p>
                        <p><strong>Price:</strong> {{ $order->final_price_with_discount }}</p>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Images</div>
                    <div class="card-body">
                        <p><strong>Front Image:</strong></p>
                        @if ($order->frontImage)
                            <img src="{{ asset('storage/' . $order->frontImage->path) }}" class="img-thumbnail"
                                alt="Front Image">
                        @else
                            <p>No image available</p>
                        @endif

                        <p><strong>Additional Image:</strong></p>
                        @if ($order->additionalImage)
                            <img src="{{ asset('storage/' . $order->additionalImage->path) }}" class="img-thumbnail"
                                alt="Additional Image">
                        @else
                            <p>No image available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Other Details -->
            <div class="col-md-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="d-flex justify-content-between align-items-center pl-3">
                        <h4 class="mb-0 text-primary">Other Details</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <p><strong>Discount Code:</strong> {{ $order->discountCode->code ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> {{ $order->status }}</p>
                        <p><strong>Note:</strong> {{ $order->note }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
