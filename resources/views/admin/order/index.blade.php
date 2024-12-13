@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h1 class="mb-0 text-primary">Orders</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="orders-table" class="table table-hover table-striped ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Username</th>
                                    <th>Order</th>
                                    <th>Governorate</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Phone2</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Change Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="statusChangeForm">
                        <input type="hidden" id="modalOrderId">
                        <div class="mb-3">
                            <label for="newStatus" class="form-label">Select Status</label>
                            <select id="newStatus" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="Preparing">Preparing</option>
                                <option value="Out for Delivery">Out for Delivery</option>
                                <option value="Completed">Completed</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ route('orders.fetch') }}',
                    error: function(xhr, error, code) {
                        alert('An error occurred while fetching data. Please contact with it team.');
                    }
                },
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'data',
                        name: 'data',
                        orderable: false
                    },
                    {
                        data: 'username',
                        name: 'username',
                        orderable: false
                    },
                    {
                        data: 'order',
                        name: 'order',
                        orderable: false
                    },
                    {
                        data: 'governorate',
                        name: 'governorate',
                        orderable: false
                    },
                    {
                        data: 'address',
                        name: 'address',
                        orderable: false
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false
                    },
                    {
                        data: 'phone2',
                        name: 'phone2',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            const statusColors = {
                                'Pending': 'bg-warning', // Yellow
                                'preparing': 'bg-primary', // Blue
                                'Out for Delivery': 'bg-info', // Teal
                                'Completed': 'bg-success', // Green
                                'Canceled': 'bg-danger' // Red
                            };

                            const dropdownItems = ['Pending', 'preparing', 'Out for Delivery',
                                    'Completed', 'Canceled'
                                ]
                                .filter(status => status !== data) // Exclude the current status
                                .map(status => `
                                    <li>
                                        <a class="dropdown-item change-status-item" 
                                        href="#" 
                                        data-order-id="${row.id}" 
                                        data-new-status="${status}">
                                            ${status}
                                        </a>
                                    </li>
                                `).join('');
                            return `
                                <div class="dropdown">
                                    <span 
                                        class="badge ${statusColors[data]} dropdown-toggle" 
                                        id="statusDropdown${row.id}" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false" 
                                        style="cursor: pointer;">
                                        ${data}
                                    </span>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown${row.id}">
                                        ${dropdownItems}
                                    </ul>
                                </div>`;
                        },
                        orderable: false
                    },
                    {
                        data: 'price',
                        name: 'price',
                        orderable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                ],
                language: {
                    search: "Search Orders:",
                    processing: '<div class="spinner-border text-primary"></div>'
                }

            });
        });
        $(document).on('click', '.change-status-item', function(e) {
            e.preventDefault();

            const orderId = $(this).data('order-id');
            const newStatus = $(this).data('new-status');

            $.ajax({
                url: '{{ route('orders.updateStatus') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: orderId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        $('#orders-table').DataTable().ajax.reload(); // Reload table data
                    } else {
                        alert('Failed to update order status. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred while updating the status.');
                }
            });
        });
    </script>
@endsection
