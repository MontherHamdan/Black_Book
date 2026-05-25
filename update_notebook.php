<?php
$content = file_get_contents('app/Http/Controllers/NotebookBindingController.php');
if (strpos($content, 'markPrinted') === false) {
    $newMethods = <<<METHODS

    /**
     * Mark a single order as Printed.
     */
    public function markPrinted(Request request)
    {
        request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        \$order = Order::findOrFail(request->order_id);
        \$order->status = 'Printed';
        \$order->designer_done = true;
        if (! \$order->designer_done_at) {
            \$order->designer_done_at = now();
        }
        
        // Calculate commission if needed (you might need to copy the method or just let it be)
        \$order->save();

        return response()->json(['success' => true]);
    }

    /**
     * Bulk mark orders as Printed.
     */
    public function bulkMarkPrinted(Request request)
    {
        request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        Order::whereIn('id', request->order_ids)->update([
            'status' => 'Printed',
            'designer_done' => true,
        ]);

        return response()->json(['success' => true]);
    }
METHODS;

    $content = str_replace('Request $request)', 'Request $request)', $content);
}
