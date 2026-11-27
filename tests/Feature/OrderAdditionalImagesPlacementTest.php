<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\Support\CustomDesignTestSchema;
use Tests\TestCase;

class OrderAdditionalImagesPlacementTest extends TestCase
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

    public function test_additional_images_placement_accepts_beginning_distributed_and_nullable(): void
    {
        $this->postJson('/api/v1/orders', $this->orderPayload([
            'additional_images_placement' => 'beginning',
        ]))
            ->assertCreated()
            ->assertJsonPath('order.additional_images_placement', 'beginning');

        $this->postJson('/api/v1/orders', $this->orderPayload([
            'additional_images_placement' => 'distributed',
        ]))
            ->assertCreated()
            ->assertJsonPath('order.additional_images_placement', 'distributed');

        $this->postJson('/api/v1/orders', $this->orderPayload([
            'additional_images_placement' => null,
        ]))
            ->assertCreated()
            ->assertJsonPath('order.additional_images_placement', null);

        $this->postJson('/api/v1/orders', $this->orderPayload())
            ->assertCreated()
            ->assertJsonPath('order.additional_images_placement', null);

        $this->assertSame(4, Order::count());
        $this->assertSame('beginning', Order::findOrFail(1)->additional_images_placement);
        $this->assertSame('distributed', Order::findOrFail(2)->additional_images_placement);
        $this->assertNull(Order::findOrFail(3)->additional_images_placement);
        $this->assertNull(Order::findOrFail(4)->additional_images_placement);
    }

    public function test_additional_images_placement_rejects_invalid_values(): void
    {
        $this->postJson('/api/v1/orders', $this->orderPayload([
            'additional_images_placement' => 'middle',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('additional_images_placement');

        $this->postJson('/api/v1/orders', $this->orderPayload([
            'additional_images_placement' => 123,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('additional_images_placement');

        $this->assertSame(0, Order::count());
    }
}
