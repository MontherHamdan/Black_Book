<div class="dropdown">
    <a class="dropdown-toggle" id="dropdownMenuButton{{ $order->id }}" data-bs-toggle="dropdown" style="cursor: pointer;"
        aria-expanded="false" title="Actions">
        <i class="fas fa-ellipsis-h"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $order->id }}">
        <li>
            <a href="#" class="dropdown-item" title="Edit Decoration">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        </li>
        <li>
            <button type="button" class="dropdown-item text-danger sa-warning-btn">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        </li>
    </ul>
</div>
