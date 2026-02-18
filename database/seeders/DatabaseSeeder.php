<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaxType;
use App\Enums\PaymentStatus;
use App\Enums\Season;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Partner;
use App\Models\PartnerPriceList;
use App\Models\Payment;
use App\Models\PickupPoint;
use App\Models\Tour;
use App\Models\TourDeparture;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

/**
 * Main database seeder for development and testing.
 */
final class DatabaseSeeder extends Seeder
{
    use WithFaker;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->setUpFaker();

        $this->createUsers();
        $this->createPickupPoints();
        $this->createTours();
        $this->createPartners();
        $this->createPriceLists();
        $this->createDepartures();
        $this->createBookings();
        $this->createPayments();
    }

    /**
     * Create admin and test users.
     */
    private function createUsers(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@magship.test',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // Driver user
        User::factory()->create([
            'name' => 'Mario Rossi',
            'email' => 'driver@magship.test',
            'password' => Hash::make('password'),
            'role' => UserRole::DRIVER,
        ]);
    }

    /**
     * Create pickup points.
     */
    private function createPickupPoints(): void
    {
        $pickups = [
            ['name' => 'MAIN ROAD', 'location' => 'Via Correale, Sorrento', 'time' => '07:25'],
            ['name' => 'HOTEL TUNNEL', 'location' => 'Via Capo, Sorrento', 'time' => '07:30'],
            ['name' => 'PIAZZA TASSO', 'location' => 'Piazza Tasso, Sorrento', 'time' => '07:45'],
            ['name' => 'PORTO', 'location' => 'Marina Piccola, Sorrento', 'time' => '08:00'],
            ['name' => 'SANT\'AGNELLO', 'location' => 'Via Crawford, Sant\'Agnello', 'time' => '07:20'],
            ['name' => 'PIANO DI SORRENTO', 'location' => 'Piazza Cota, Piano di Sorrento', 'time' => '07:15'],
            ['name' => 'META', 'location' => 'Via Caracciolo, Meta', 'time' => '07:10'],
            ['name' => 'VICO EQUENSE', 'location' => 'Corso Umberto, Vico Equense', 'time' => '07:00'],
        ];

        foreach ($pickups as $index => $pickup) {
            PickupPoint::create([
                'name' => $pickup['name'],
                'location' => $pickup['location'],
                'default_time' => $pickup['time'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Create tour products.
     */
    private function createTours(): void
    {
        $tours = [
            ['code' => 'POSAMCL', 'name' => 'Positano, Amalfi Coast & Limoncello', 'capacity' => 50],
            ['code' => 'CAPRI', 'name' => 'Capri Island Day Trip', 'capacity' => 40],
            ['code' => 'POMPEI', 'name' => 'Pompeii Archaeological Tour', 'capacity' => 50],
            ['code' => 'RAVELLO', 'name' => 'Ravello Gardens & Villa Tour', 'capacity' => 30],
            ['code' => 'VESUVIO', 'name' => 'Mount Vesuvius Hiking', 'capacity' => 25],
        ];

        foreach ($tours as $tour) {
            Tour::create([
                'code' => $tour['code'],
                'name' => $tour['name'],
                'description' => "Experience the beauty of {$tour['name']} with our expert guides.",
                'seasonality_start' => now()->year . '-01-01',
                'seasonality_end' => now()->year . '-12-31',
                'cutoff_hours' => 24,
                'default_capacity' => $tour['capacity'],
                'is_active' => true,
            ]);
        }
    }

    /**
     * Create B2B partners.
     */
    private function createPartners(): void
    {
        $partners = [
            ['name' => 'Hotel Excelsior Vittoria', 'email' => 'bookings@excelsiorvittoria.com'],
            ['name' => 'Grand Hotel Capodimonte', 'email' => 'tours@capodimonte.it'],
            ['name' => 'Hotel Bristol', 'email' => 'concierge@hotelbristol.it'],
            ['name' => 'Maison La Minervetta', 'email' => 'info@laminervetta.com'],
            ['name' => 'Sorrento Tours & Travel', 'email' => 'booking@sorrentotours.it'],
        ];

        foreach ($partners as $partner) {
            $p = Partner::factory()->hotel()->create([
                'name' => $partner['name'],
                'email' => $partner['email'],
            ]);

            // Create a user for this partner
            User::factory()->create([
                'name' => "{$partner['name']} Staff",
                'email' => str_replace('@', '+staff@', $partner['email']),
                'password' => Hash::make('password'),
                'role' => UserRole::PARTNER,
                'partner_id' => $p->id,
            ]);
        }
    }

    /**
     * Create price lists for all partner/tour combinations.
     */
    private function createPriceLists(): void
    {
        $partners = Partner::all();
        $tours = Tour::all();

        foreach ($partners as $partner) {
            foreach ($tours as $tour) {
                // Mid season prices
                PartnerPriceList::create([
                    'partner_id' => $partner->id,
                    'tour_id' => $tour->id,
                    'season' => Season::MID,
                    'pax_type' => PaxType::ADULT,
                    'price' => $this->faker->randomFloat(2, 45, 65),
                ]);
                PartnerPriceList::create([
                    'partner_id' => $partner->id,
                    'tour_id' => $tour->id,
                    'season' => Season::MID,
                    'pax_type' => PaxType::CHILD,
                    'price' => $this->faker->randomFloat(2, 25, 40),
                ]);

                // High season prices (20-30% higher)
                PartnerPriceList::create([
                    'partner_id' => $partner->id,
                    'tour_id' => $tour->id,
                    'season' => Season::HIGH,
                    'pax_type' => PaxType::ADULT,
                    'price' => $this->faker->randomFloat(2, 55, 80),
                ]);
                PartnerPriceList::create([
                    'partner_id' => $partner->id,
                    'tour_id' => $tour->id,
                    'season' => Season::HIGH,
                    'pax_type' => PaxType::CHILD,
                    'price' => $this->faker->randomFloat(2, 35, 55),
                ]);
            }
        }
    }

    /**
     * Create tour departures for the next 3 months.
     */
    private function createDepartures(): void
    {
        $tours = Tour::all();
        $startDate = now()->startOfDay();
        $endDate = now()->addMonths(3);

        // Get the driver user to assign to departures
        $driver = User::where('role', UserRole::DRIVER)->first();

        foreach ($tours as $tour) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $month = (int) $currentDate->format('n');

                // Only create departures within tour's seasonality
                if ($tour->isInSeason($month)) {
                    // Assign driver to departures in the next 2 weeks
                    $isUpcoming = $currentDate->diffInDays(now()) <= 14;

                    TourDeparture::create([
                        'tour_id' => $tour->id,
                        'driver_id' => $isUpcoming ? $driver?->id : null,
                        'date' => $currentDate->format('Y-m-d'),
                        'time' => '08:30',
                        'capacity' => $tour->default_capacity,
                        'status' => 'open',
                        'season' => Season::fromMonth($month),
                    ]);
                }

                $currentDate->addDay();
            }
        }
    }

    /**
     * Create sample bookings with passengers.
     */
    private function createBookings(): void
    {
        $partners = Partner::all();
        $pickupPoints = PickupPoint::all();

        // Get departures in the next 2 weeks
        $departures = TourDeparture::where('date', '>=', now())
            ->where('date', '<=', now()->addWeeks(2))
            ->inRandomOrder()
            ->limit(30)
            ->get();

        foreach ($departures as $departure) {
            $partner = $partners->random();

            // Create 1-3 bookings per departure
            $bookingCount = $this->faker->numberBetween(1, 3);

            for ($i = 0; $i < $bookingCount; $i++) {
                $adults = $this->faker->numberBetween(1, 4);
                $children = $this->faker->numberBetween(0, 2);
                $infants = $children > 0 ? $this->faker->numberBetween(0, 1) : 0;

                // Calculate total based on price lists
                $adultPrice = PartnerPriceList::getPriceFor(
                    $partner->id,
                    $departure->tour_id,
                    $departure->season,
                    PaxType::ADULT
                ) ?? 50.00;

                $childPrice = PartnerPriceList::getPriceFor(
                    $partner->id,
                    $departure->tour_id,
                    $departure->season,
                    PaxType::CHILD
                ) ?? 30.00;

                $totalAmount = ($adults * $adultPrice) + ($children * $childPrice);

                $booking = Booking::create([
                    'booking_code' => Booking::generateBookingCode($departure),
                    'partner_id' => $partner->id,
                    'tour_departure_id' => $departure->id,
                    'status' => $this->faker->randomElement([
                        BookingStatus::CONFIRMED,
                        BookingStatus::CONFIRMED,
                        BookingStatus::CONFIRMED,
                        BookingStatus::SUSPENDED_REQUEST,
                    ]),
                    'total_amount' => $totalAmount,
                    'payment_status' => $this->faker->randomElement([
                        PaymentStatus::UNPAID,
                        PaymentStatus::UNPAID,
                        PaymentStatus::PARTIAL,
                        PaymentStatus::PAID,
                    ]),
                    'suspended_until' => null,
                ]);

                // Set suspended_until for overbooking requests
                if ($booking->status === BookingStatus::SUSPENDED_REQUEST) {
                    $booking->update(['suspended_until' => now()->addHours(2)]);
                }

                // Create passengers
                $pickupPoint = $pickupPoints->random();

                for ($j = 0; $j < $adults; $j++) {
                    // 15% chance of having allergies
                    $allergies = $this->faker->boolean(15)
                        ? $this->faker->randomElement(['Shellfish', 'Gluten', 'Nuts', 'Dairy', 'Eggs'])
                        : null;

                    BookingPassenger::create([
                        'booking_id' => $booking->id,
                        'pickup_point_id' => $pickupPoint->id,
                        'first_name' => $this->faker->firstName(),
                        'last_name' => $this->faker->lastName(),
                        'pax_type' => PaxType::ADULT,
                        'phone' => $j === 0 ? $this->faker->phoneNumber() : null,
                        'allergies' => $allergies,
                        'price' => $adultPrice,
                    ]);
                }

                for ($j = 0; $j < $children; $j++) {
                    BookingPassenger::create([
                        'booking_id' => $booking->id,
                        'pickup_point_id' => $pickupPoint->id,
                        'first_name' => $this->faker->firstName(),
                        'last_name' => $this->faker->lastName(),
                        'pax_type' => PaxType::CHILD,
                        'price' => $childPrice,
                    ]);
                }

                for ($j = 0; $j < $infants; $j++) {
                    BookingPassenger::create([
                        'booking_id' => $booking->id,
                        'pickup_point_id' => $pickupPoint->id,
                        'first_name' => $this->faker->firstName(),
                        'last_name' => $this->faker->lastName(),
                        'pax_type' => PaxType::INFANT,
                        'price' => 0,
                    ]);
                }
            }
        }
    }

    /**
     * Create sample payments.
     */
    private function createPayments(): void
    {
        $partners = Partner::all();

        foreach ($partners as $partner) {
            // Create 1-3 payments per partner
            $paymentCount = $this->faker->numberBetween(1, 3);

            for ($i = 0; $i < $paymentCount; $i++) {
                Payment::create([
                    'partner_id' => $partner->id,
                    'amount' => $this->faker->randomFloat(2, 200, 1500),
                    'method' => $this->faker->randomElement(['bank_transfer', 'cash']),
                    'reference' => $this->faker->optional(0.7)->numerify('TRX-######'),
                    'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }
    }
}
