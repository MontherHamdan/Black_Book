<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the pricing rules for book orders.
    | All prices are in JOD (Jordanian Dinar).
    |
    */

    // Additional costs for optional features
    'sponge_cost' => 0, // Cost if sponge is enabled (is_sponge = true)
    
    // Page pricing
    'page_base_count' => 50, // Number of pages included in base price
    'cost_per_extra_page' => 0.5, // Cost per page above base count
    
    // Image pricing
    'back_image_cost_per_image' => 0, // Cost per back image
    'transparent_printing_cost_per_image' => 0, // Cost per transparent printing image
    'additional_image_cost_per_image' => 0, // Cost per additional image
    
    // Other features
    'gift_custom_cost' => 0, // Cost for custom gift
    'gift_default_cost' => 0, // Cost for default gift
    'with_additives_cost' => 0, // Cost if is_with_additives is true
    
    // Book decoration pricing (can be extended with specific decoration prices)
    'decoration_cost' => 0, // Base decoration cost
];

