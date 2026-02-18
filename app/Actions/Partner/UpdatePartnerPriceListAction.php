<?php

declare(strict_types=1);

namespace App\Actions\Partner;

use App\Enums\PaxType;
use App\Enums\Season;
use App\Models\Partner;
use App\Models\PartnerPriceList;
use Illuminate\Support\Facades\DB;

/**
 * Updates the price list for a partner.
 *
 * Handles the matrix of tour x season x pax_type prices.
 */
final class UpdatePartnerPriceListAction
{
    /**
     * Execute the price list update.
     *
     * @param Partner $partner The partner to update prices for
     * @param array<int, array<string, array<string, float|null>>> $prices Matrix of [tour_id => [season => [pax_type => price]]]
     * @return int Number of price entries created or updated
     */
    public function execute(Partner $partner, array $prices): int
    {
        $count = 0;

        DB::transaction(function () use ($partner, $prices, &$count) {
            foreach ($prices as $tourId => $seasons) {
                foreach ($seasons as $seasonValue => $paxTypes) {
                    $season = Season::tryFrom($seasonValue);
                    if (!$season) {
                        continue;
                    }

                    foreach ($paxTypes as $paxTypeValue => $price) {
                        // Skip null or empty prices
                        if ($price === null || $price === '') {
                            // Delete existing price if set to null
                            PartnerPriceList::where([
                                'partner_id' => $partner->id,
                                'tour_id' => $tourId,
                                'season' => $season,
                                'pax_type' => $paxTypeValue,
                            ])->delete();
                            continue;
                        }

                        $paxType = PaxType::tryFrom($paxTypeValue);
                        if (!$paxType) {
                            continue;
                        }

                        PartnerPriceList::updateOrCreate(
                            [
                                'partner_id' => $partner->id,
                                'tour_id' => $tourId,
                                'season' => $season,
                                'pax_type' => $paxType,
                            ],
                            [
                                'price' => (float) $price,
                            ]
                        );

                        $count++;
                    }
                }
            }
        });

        return $count;
    }
}
