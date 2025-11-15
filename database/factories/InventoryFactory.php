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
        $itemTypes = ['medication', 'lab_supply', 'equipment'];
        $itemType = fake()->randomElement($itemTypes);

        $units = ['tablets', 'capsules', 'boxes', 'pieces', 'ml', 'liters', 'units', 'kits'];
        $categories = [
            'medication' => ['Antibiotic', 'Analgesic', 'Antiviral', 'Vitamin'],
            'lab_supply' => ['Reagent', 'Consumable', 'Test Kit', 'Chemical'],
            'equipment' => ['PPE', 'Medical Supply', 'Surgical Item'],
        ];

        $quantity = fake()->numberBetween(10, 500);
        $minimumStock = fake()->numberBetween(5, 100);

        return [
            'item_name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'item_type' => $itemType,
            'item_id' => null, // Will be set when creating with specific item
            'category' => fake()->randomElement($categories[$itemType]),
            'quantity' => $quantity,
            'unit' => fake()->randomElement($units),
            'minimum_stock' => $minimumStock,
            'unit_price' => fake()->randomFloat(2, 1, 200),
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
            'expiry_date' => $itemType !== 'equipment' ? fake()->dateTimeBetween('now', '+3 years') : null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the inventory is for a medication
     */
    public function forMedication(\App\Models\Medication $medication): static
    {
        return $this->state(fn (array $attributes) => [
            'item_name' => $medication->name,
            'description' => $medication->description,
            'item_type' => 'medication',
            'item_id' => $medication->id,
            'category' => $medication->dosage_form ?? 'General',
        ]);
    }

    /**
     * Indicate that the inventory is for a lab supply
     */
    public function forLabSupply(\App\Models\LabSupply $labSupply): static
    {
        return $this->state(fn (array $attributes) => [
            'item_name' => $labSupply->name,
            'description' => "Laboratory supply: {$labSupply->category}",
            'item_type' => 'lab_supply',
            'item_id' => $labSupply->id,
            'category' => $labSupply->category,
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
