<?php

namespace Tests\Feature;

use App\Models\CustomDesignUpload;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Support\CustomDesignTestSchema;
use Tests\TestCase;

class CustomDesignUploadTest extends TestCase
{
    use CustomDesignTestSchema;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createDesignTestSchema();
        Storage::fake('local');
        $this->token = (string) Str::uuid();
    }

    private function upload(string $purpose = 'decoration', ?UploadedFile $file = null): array
    {
        return $this->postJson('/api/v1/custom-designs', [
            'purpose' => $purpose,
            'upload_token' => $this->token,
            'file' => $file ?? UploadedFile::fake()->createWithContent('تصميم.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"),
        ])->assertCreated()->json('data');
    }

    private function orderData(array $extra = []): array
    {
        return array_merge([
            'user_gender' => 'female', 'book_type_id' => 1, 'book_design_id' => 1,
            'user_type' => 'university', 'username_ar' => 'ليان', 'username_en' => 'Layan',
            'user_phone_number' => '0790000000', 'is_sponge' => false, 'pages_number' => 150,
            'delivery_number_one' => '0790000000', 'delivery_target' => 'university',
            'university_id' => 1, 'university_major_id' => 1, 'delivery_university_id' => 1,
            'gift_type' => 'none',
        ], $extra);
    }

    public function test_pdf_and_image_are_preserved_and_linked_to_order_at_existing_prices(): void
    {
        $decoration = $this->upload();
        $gift = $this->upload('gift', UploadedFile::fake()->image('اهداء.png'));
        $this->postJson('/api/v1/orders', $this->orderData([
            'custom_decoration_upload_id' => $decoration['id'],
            'custom_gift_upload_id' => $gift['id'],
            'design_upload_token' => $this->token,
            'gift_type' => 'custom',
        ]))->assertCreated()->assertJsonPath('pricing.final_price', 18.5)
            ->assertJsonPath('order.custom_decoration_upload.id', $decoration['id'])
            ->assertJsonPath('order.custom_gift_upload.id', $gift['id']);
        $order = Order::firstOrFail();
        $this->assertTrue($order->hasDecoration());
        $this->assertNull($order->gift_title);
        $this->assertNotNull($order->customDecorationUpload->claimed_at);
        Storage::disk('local')->assertExists($order->customDecorationUpload->path);
        Storage::disk('local')->assertExists($order->customGiftUpload->path);
        $this->assertArrayNotHasKey('path', $decoration);
        $this->assertArrayNotHasKey('token_hash', $decoration);
    }

    public function test_admin_and_designer_can_download_exact_originals_and_see_links(): void
    {
        $file = $this->upload();
        $gift = $this->upload('gift', UploadedFile::fake()->image('gift.png'));
        $this->postJson('/api/v1/orders', $this->orderData([
            'custom_decoration_upload_id' => $file['id'], 'design_upload_token' => $this->token,
            'custom_gift_upload_id' => $gift['id'], 'gift_type' => 'custom',
        ]))->assertCreated();
        $order = Order::firstOrFail();
        $bytes = Storage::disk('local')->get($order->customDecorationUpload->path);
        foreach (['admin', 'designer'] as $role) {
            $this->actingAs((new User)->forceFill(['id' => 123, 'role' => $role]));
            $response = $this->get(route('orders.customerDesign', [$order, 'decoration']))
                ->assertOk()->assertDownload()->assertStreamedContent($bytes);
            $this->assertStringContainsString(rawurlencode('تصميم.pdf'), $response->headers->get('Content-Disposition'));
            $this->get(route('orders.customerDesign', [$order, 'decoration', 'preview' => 1]))
                ->assertOk()->assertHeader('Content-Type', 'application/pdf');
            $this->get(route('orders.customerDesign', [$order, 'gift']))
                ->assertOk()->assertDownload('gift.png')
                ->assertStreamedContent(Storage::disk('local')->get($order->customGiftUpload->path));
        }
        $this->view('admin.order.customer-design', [
            'order' => $order, 'upload' => $order->customDecorationUpload, 'label' => 'الزخرفة',
        ])->assertSee('تنزيل الملف الأصلي')->assertSee('تصميم.pdf');
        $this->actingAs((new User)->forceFill(['id' => 124, 'role' => 'customer']));
        $this->getJson(route('orders.customerDesign', [$order, 'decoration']))->assertForbidden();
    }

    public function test_previews_require_signed_url_and_admin_download_requires_login(): void
    {
        $file = $this->upload();
        $this->get($file['preview_url'])->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->getJson('/api/v1/custom-designs/'.$file['id'])->assertForbidden();
        $this->getJson('/orders/1/customer-design/decoration')->assertUnauthorized();
    }

    public function test_wrong_owner_wrong_purpose_and_reused_upload_are_rejected_atomically(): void
    {
        $decoration = $this->upload();
        $gift = $this->upload('gift');
        $payload = $this->orderData([
            'custom_decoration_upload_id' => $decoration['id'], 'design_upload_token' => (string) Str::uuid(),
        ]);
        $this->postJson('/api/v1/orders', $payload)->assertUnprocessable()->assertJsonValidationErrors('custom_decoration_upload_id');
        $payload['design_upload_token'] = $this->token;
        $payload['custom_gift_upload_id'] = $decoration['id'];
        $payload['gift_type'] = 'custom';
        $this->postJson('/api/v1/orders', $payload)->assertUnprocessable()->assertJsonValidationErrors('custom_gift_upload_id');
        $this->assertSame(0, Order::count());
        $this->assertNull(CustomDesignUpload::findOrFail($decoration['id'])->claimed_at);
        $payload['custom_gift_upload_id'] = $gift['id'];
        $this->postJson('/api/v1/orders', $payload)->assertCreated();
        $this->postJson('/api/v1/orders', $payload)->assertUnprocessable();
        $this->assertSame(1, Order::count());
    }

    public function test_invalid_file_types_and_oversized_files_are_rejected(): void
    {
        $invalidFiles = [
            UploadedFile::fake()->createWithContent('fake.pdf', '<html>not a PDF</html>'),
            UploadedFile::fake()->createWithContent('image.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        ];
        foreach ($invalidFiles as $fake) {
            // A real UploadedFile lets fileinfo inspect bytes; Laravel's fake
            // reports a MIME type based on the filename instead.
            $file = new UploadedFile($fake->getPathname(), $fake->getClientOriginalName(), null, null, true);
            $this->postJson('/api/v1/custom-designs', ['purpose' => 'decoration', 'upload_token' => $this->token, 'file' => $file])
                ->assertUnprocessable()->assertJsonValidationErrors('file');
        }
        $this->postJson('/api/v1/custom-designs', ['purpose' => 'decoration', 'upload_token' => $this->token,
            'file' => UploadedFile::fake()->create('large.pdf', 20481, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors('file');
        $this->assertSame(0, CustomDesignUpload::count());
    }

    public function test_draft_deletion_checks_ownership_and_never_deletes_order_files(): void
    {
        $file = $this->upload();
        $path = CustomDesignUpload::findOrFail($file['id'])->path;
        $this->deleteJson('/api/v1/custom-designs/'.$file['id'], ['upload_token' => (string) Str::uuid()])->assertForbidden();
        $this->deleteJson('/api/v1/custom-designs/'.$file['id'], ['upload_token' => $this->token])->assertNoContent();
        Storage::disk('local')->assertMissing($path);
        $file = $this->upload();
        $this->postJson('/api/v1/orders', $this->orderData(['custom_decoration_upload_id' => $file['id'], 'design_upload_token' => $this->token]))->assertCreated();
        $this->deleteJson('/api/v1/custom-designs/'.$file['id'], ['upload_token' => $this->token])->assertConflict();
        $order = Order::firstOrFail();
        $path = $order->customDecorationUpload->path;
        $order->delete();
        Storage::disk('local')->assertExists($path);
        $order->restore();
        $order->forceDelete();
        Storage::disk('local')->assertMissing($path);
    }

    public function test_ready_decoration_and_text_gift_keep_existing_behavior(): void
    {
        $this->postJson('/api/v1/orders', $this->orderData(['book_decorations_id' => 1, 'gift_type' => 'custom']))
            ->assertUnprocessable()->assertJsonValidationErrors('gift_title');
        $this->postJson('/api/v1/orders', $this->orderData(['book_decorations_id' => 1, 'gift_type' => 'custom', 'gift_title' => 'شكراً لكم']))
            ->assertCreated()->assertJsonPath('pricing.final_price', 18.5);
        $this->postJson('/api/v1/orders', $this->orderData())->assertCreated()->assertJsonPath('pricing.final_price', 13);
    }

    public function test_conflicting_design_choices_and_missing_file_are_rejected(): void
    {
        $file = $this->upload();
        $payload = $this->orderData(['custom_decoration_upload_id' => $file['id'], 'design_upload_token' => $this->token]);
        $this->postJson('/api/v1/orders', $payload + ['book_decorations_id' => 1])->assertUnprocessable();
        $this->postJson('/api/v1/orders', $this->orderData(['custom_gift_upload_id' => $file['id'], 'design_upload_token' => $this->token]))->assertUnprocessable();
        Storage::disk('local')->delete(CustomDesignUpload::findOrFail($file['id'])->path);
        $this->postJson('/api/v1/orders', $payload)->assertUnprocessable();
        $this->assertSame(0, Order::count());
    }

    public function test_product_question_required_optional_and_disabled_rules(): void
    {
        $product = \App\Models\BookType::findOrFail(1);
        $product->update(['question_enabled' => true, 'question_text' => 'ما الاسم المطلوب؟', 'question_required' => true]);
        $payload = $this->orderData(['product_question' => 'ما الاسم المطلوب؟', 'product_answer' => '   ']);
        $this->postJson('/api/v1/orders', $payload)->assertUnprocessable()->assertJsonValidationErrors('product_answer');
        $payload['product_answer'] = 'ليان أحمد';
        $this->postJson('/api/v1/orders', $payload)->assertCreated()->assertJsonPath('order.product_answer', 'ليان أحمد')
            ->assertJsonPath('order.product_question', 'ما الاسم المطلوب؟')->assertJsonPath('pricing.final_price', 13);
        $order = Order::firstOrFail();
        $product->update(['question_text' => 'ما العبارة المطلوبة؟', 'question_required' => false]);
        $this->assertSame('ما الاسم المطلوب؟', $order->fresh()->product_question);
        $this->postJson('/api/v1/orders', $payload)->assertUnprocessable();
        $this->postJson('/api/v1/orders', $this->orderData(['product_question' => 'ما العبارة المطلوبة؟']))
            ->assertCreated()->assertJsonPath('order.product_answer', null);
        $product->update(['question_enabled' => false]);
        $this->postJson('/api/v1/orders', $payload)->assertCreated()->assertJsonPath('order.product_question', null)->assertJsonPath('order.product_answer', null);
        $this->view('admin.order.product-question', ['order' => $order])->assertSee('ما الاسم المطلوب؟')->assertSee('ليان أحمد');
    }

    public function test_admin_can_configure_product_question_and_blank_prompt_is_rejected(): void
    {
        $this->actingAs((new User)->forceFill(['id' => 123, 'role' => 'admin']));
        $settings = ['name_ar' => 'ستاند', 'name_en' => 'Stand', 'price' => 13,
            'question_enabled' => 1, 'question_required' => 1, 'question_text' => ''];
        $this->putJson('/book-types/1', $settings)->assertUnprocessable()->assertJsonValidationErrors('question_text');
        $settings['question_text'] = 'اكتب الاسم';
        $this->putJson('/book-types/1', $settings)->assertRedirect();
        $product = \App\Models\BookType::findOrFail(1);
        $this->assertTrue($product->question_enabled);
        $this->assertTrue($product->question_required);
        $settings['question_enabled'] = 0;
        $this->putJson('/book-types/1', $settings)->assertRedirect();
        $this->assertFalse($product->fresh()->question_required);
        $this->putJson('/book-types/1/question', [
            'question_enabled' => 1, 'question_text' => 'اسم الستاند؟', 'question_required' => 1,
        ])->assertRedirect(route('book-types.question.edit', 1));
        $this->assertSame('اسم الستاند؟', $product->fresh()->question_text);
        $this->assertEquals(13, $product->fresh()->price);
        $this->putJson('/book-types/1/question', [
            'question_enabled' => 0, 'question_text' => 'اسم الستاند؟', 'question_required' => 1,
        ])->assertRedirect();
        $this->assertFalse($product->fresh()->question_enabled);
        $this->assertSame('اسم الستاند؟', $product->fresh()->question_text);
        $this->actingAs((new User)->forceFill(['id' => 124, 'role' => 'designer']));
        $this->putJson('/book-types/1/question', [
            'question_enabled' => 1, 'question_text' => 'سؤال', 'question_required' => 0,
        ])->assertForbidden();
        $this->assertFalse($product->fresh()->question_enabled);

    }
}
