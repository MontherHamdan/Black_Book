@extends('admin.layout')

@section('content')
<div class="container">
    <h1 class="my-4 text-center">Order Details</h1>

    <div class="row">
        <!-- Left Side: Order Details and Other Information -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-box-seam me-2"></i> Order Details
                </div>
                <div class="card-body">
                    <p><strong>Book Type:</strong> {{ $order->bookType->name_ar ?? 'N/A' }}</p>
                    <p><strong>Design:</strong></p>
                    <div class="d-flex justify-content-start">
                        <img class="img-fluid img-thumbnail" src="{{ $order->bookDesign->image }}" alt="Design Image" style="max-width: 200px;">
                    </div>
                    <p class="mt-1"><strong>Pages Number:</strong> {{ $order->pages_number }}</p>
                    <p><strong>Price:</strong> {{ $order->final_price }}</p>
                    <p><strong>Price with discount:</strong> {{ $order->final_price_with_discount }}</p>
                    <p><strong>Accessory:</strong> {{ $order->book_accessory ? 'Yes' : 'No' }}</p>
                    <p><strong>Sponge:</strong> {{ $order->is_sponge ? 'Yes' : 'No' }}</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i> Other Details
                </div>
                <div class="card-body">
                    <p><strong>Discount Code:</strong> {{ $order->discountCode->discount_code ?? 'N/A' }}</p>
                    <p><strong>Discount Value:</strong> 
                        @if ($order->discountCode)
                            {{ $order->discountCode->discount_value }} 
                            {{ $order->discountCode->discount_type === 'percentage' ? '%' : 'JOD' }}
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Discount Type:</strong>
                        @if ($order->discountCode)
                            {{ ucfirst($order->discountCode->discount_type) }}
                        @else
                            N/A
                        @endif
                    </p>                  
                    <p><strong>SVG Title:</strong> {{ $order->svg_title ?? 'N/A' }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $order->status == 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p><strong>Note:</strong> {{ $order->note ?? 'No notes provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Right Side: User Details, Address, and More -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-person-circle me-2"></i> User Details
                </div>
                <div class="card-body">
                    <p><strong>Name (AR):</strong> {{ $order->username_ar }}</p>
                    <p><strong>Name (EN):</strong> {{ $order->username_en }}</p>
                    <p><strong>Phone:</strong> {{ $order->user_phone_number }}</p>
                    <p><strong>Alternate Phone:</strong> {{ $order->delivery_number_two ?? 'N/A' }}</p>
                    <p><strong>Gender:</strong> {{ ucfirst($order->user_gender) }}</p>
                    <p><strong>User Type:</strong> {{ ucfirst($order->user_type) }}</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-geo-alt me-2"></i> Address
                </div>
                <div class="card-body">
                    <p><strong>Governorate:</strong> {{ $order->governorate }}</p>
                    <p><strong>Address:</strong> {{ $order->address }}</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-book me-2"></i> Education Details
                </div>
                <div class="card-body">
                    <p><strong>School Name:</strong> {{ $order->school_name }}</p>
                    <p><strong>Major Name:</strong> {{ $order->major_name }}</p>
                </div>
            </div>
        </div>
<!-- Full-width: Images Section -->
<div class="col-12">
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center">
            <i class="bi bi-images me-2"></i> Images
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Front Image:</strong></p>
                    @if ($order->frontImage)
                        <div class="d-flex justify-content-start align-items-center">
                            <img src="{{ $order->frontImage->image_path }}" class="img-fluid img-thumbnail mb-2" alt="Front Image" style="max-width: 100%; height: auto;">
                            <a href="{{ $order->frontImage->image_path }}" class="btn btn-secondary btn-sm ms-3" download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    @else
                        <p>No image available</p>
                    @endif
                </div>

                <div class="col-md-6">
                    <p><strong>Additional Image:</strong></p>
                    @if ($order->additionalImage)
                    <div class="d-flex justify-content-start align-items-center">
                        <img src="{{ $order->additionalImage->image_path }}" class="img-fluid img-thumbnail mb-2" alt="Additional Image" style="max-width: 100%; height: auto;">
                            <a href="{{ $order->additionalImage->image_path }}" class="btn btn-secondary btn-sm ms-3" download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    @else
                        <p>No image available</p>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <p><strong>SVG:</strong></p>
                <div class="d-flex align-items-center svg-preview-container">
                    <div class="img-fluid img-thumbnail svg-preview mb-2" style="width: 80%; height: auto;">
                        {!! $order->svg->svg_code !!}
                    </div>
                    <button class="btn btn-primary btn-sm copy-svg-button ms-3">
                        <i class="fas fa-copy me-1"></i> Copy
                    </button>
                </div>
            </div>

            <p><strong>Back Images:</strong></p>
            @if ($order->backImages()->isNotEmpty())
                <div id="backImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($order->backImages() as $index => $backImage)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ $backImage->image_path }}" class="d-block w-100 img-thumbnail" alt="Back Image" style="max-height: 500px;">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#backImagesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#backImagesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('orders.backImages.download', $order->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download me-1"></i> Download All
                    </a>
                </div>
            @else
                <p>No back images available</p>
            @endif
        </div>
    </div>
</div>

    </div>
</div>
<script>
// Attach event listener to dynamically copy SVG code
document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-svg-button');

    // Create a reusable toast notification container
    const toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.style.position = 'fixed';
    toastContainer.style.bottom = '20px';
    toastContainer.style.right = '20px';
    toastContainer.style.zIndex = '9999';
    document.body.appendChild(toastContainer);

    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const svgPreviewDiv = document.querySelector('.svg-preview');
            const svgCode = svgPreviewDiv.innerHTML.trim(); // Extract the SVG code from the div

            navigator.clipboard.writeText(svgCode)
                .then(() => {
                    showToast('SVG code copied to clipboard!', 'success');
                })
                .catch(err => {
                    console.error('Failed to copy SVG code: ', err);
                    showToast('Failed to copy SVG code. Please try again.', 'error');
                });
        });
    });

    // Function to show a toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.padding = '10px 20px';
        toast.style.marginTop = '10px';
        toast.style.borderRadius = '5px';
        toast.style.color = '#fff';
        toast.style.fontSize = '14px';
        toast.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

        if (type === 'success') {
            toast.style.backgroundColor = '#28a745'; // Green for success
        } else if (type === 'error') {
            toast.style.backgroundColor = '#dc3545'; // Red for error
        }

        toastContainer.appendChild(toast);

        // Show the toast with animation
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(-10px)';
        }, 100);

        // Remove the toast after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(0)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
@endsection
