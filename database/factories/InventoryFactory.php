<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplyTypes = ['medication', 'rx_medicine', 'lab_supply', 'medical_equipment', 'office_supply', 'cleaning_supply'];

        $units = ['tablets', 'capsules', 'boxes', 'pieces', 'ml', 'liters', 'units', 'kits'];

        $quantity = fake()->numberBetween(10, 500);
        $minimumStock = fake()->numberBetween(5, 100);

        return [
            'item_name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['Antibiotic', 'Analgesic', 'Reagent', 'Consumable', 'PPE']),
            'barcode' => fake()->optional(0.5)->ean13(),
            'type_of_supply' => fake()->randomElement($supplyTypes),
            'quantity' => $quantity,
            'unit' => fake()->randomElement($units),
            'dose_unit' => fake()->optional(0.5)->randomElement(['mg', 'ml', 'g', 'mcg']),
            'total_per_box' => fake()->optional(0.5)->numberBetween(10, 100),
            'minimum_stock' => $minimumStock,
            'unit_price' => fake()->randomFloat(2, 1, 200),
            'selling_price' => fake()->randomFloat(2, 2, 300),
            'supplier' => fake()->company(),
            'location' => fake()->randomElement([
                'Main Storage',
                'Pharmacy',
                'Laboratory Storage',
                'Ward A',
                'Ward B',
                'Emergency',
                'Central Supply',
            ]),
            'expiry_date' => fake()->dateTimeBetween('now', '+3 years'),
            'alert_days' => fake()->numberBetween(7, 90),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the inventory is an RX Medicine
     */
    public function rxMedicine(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_of_supply' => 'rx_medicine',
        ]);
    }

    /**
     * Indicate that the inventory is a lab supply
     */
    public function labSupply(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_of_supply' => 'lab_supply',
        ]);
    }

    /**
     * Indicate that the inventory is low on stock
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => fake()->numberBetween(1, $attributes['minimum_stock']),
        ]);
    }

    /**
     * Indicate that the inventory is out of stock
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
        ]);
    }
}
