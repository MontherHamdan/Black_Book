<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\Support\CustomDesignTestSchema;
use Tests\TestCase;

class OrderDecorationNoteTest extends TestCase
{
    use CustomDesignTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createDesignTestSchema();
    }

    private function orderPayload(array $extra = []): array
    {
        return array_merge([
            'user_gender' => 'female',
            'book_type_id' => 1,
            'book_design_id' => 1,
            'user_type' => 'university',
            'username_ar' => 'ليان',
            'username_en' => 'Layan',
            'user_phone_number' => '0790000000',
            'is_sponge' => false,
            'pages_number' => 150,
            'delivery_number_one' => '0790000000',
            'delivery_target' => 'university',
            'university_id' => 1,
            'university_major_id' => 1,
            'delivery_university_id' => 1,
            'gift_type' => 'none',
        ], $extra);
    }

    public function test_decoration_note_is_nullable_and_persists_string(): void
    {
        $this->postJson('/api/v1/orders', $this->orderPayload([
            'decoration_note' => 'زخرفة باللون الذهبي',
        ]))
            ->assertCreated()
            ->assertJsonPath('order.decoration_note', 'زخرفة باللون الذهبي');

        $this->postJson('/api/v1/orders', $this->orderPayload([
            'decoration_note' => null,
        ]))
            ->assertCreated()
            ->assertJsonPath('order.decoration_note', null);

        $this->postJson('/api/v1/orders', $this->orderPayload())
            ->assertCreated()
            ->assertJsonPath('order.decoration_note', null);

        $this->assertSame(3, Order::count());
        $this->assertSame('زخرفة باللون الذهبي', Order::findOrFail(1)->decoration_note);
        $this->assertNull(Order::findOrFail(2)->decoration_note);
        $this->assertNull(Order::findOrFail(3)->decoration_note);
    }

    public function test_decoration_note_validates_string_and_max_length(): void
    {
        $this->postJson('/api/v1/orders', $this->orderPayload([
            'decoration_note' => str_repeat('a', 256),
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('decoration_note');

        $this->postJson('/api/v1/orders', $this->orderPayload([
            'decoration_note' => ['invalid'],
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('decoration_note');

        $this->assertSame(0, Order::count());
    }
}
