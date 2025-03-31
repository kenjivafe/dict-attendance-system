<?php

namespace App\Filament\Resources\CheckpointResource\Widgets;

use Cheesegrits\FilamentGoogleMaps\Widgets\MapWidget;

class CheckpointMap extends MapWidget
{
    protected static ?string $heading = 'Map';

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected static ?bool $clustering = true;

    protected static ?bool $fitToBounds = false;

    protected static ?int $zoom = 10;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
    	/**
    	 * You can use whatever query you want here, as long as it produces a set of records with your
    	 * lat and lng fields in them.
    	 */
        $checkpoints = \App\Models\Checkpoint::query()->limit(500)->get();

        $data = [];

        foreach ($checkpoints as $checkpoint)
        {
			/**
			 * Each element in the returned data must be an array
			 * containing a 'location' array of 'lat' and 'lng',
			 * and a 'label' string (optional but recommended by Google
			 * for accessibility.
			 *
			 * You should also include an 'id' attribute for internal use by this plugin
			 */
            $data[] = [
                'location'  => [
                    'lat' => $checkpoint->lat ? round(floatval($checkpoint->lat), static::$precision) : 0,
                    'lng' => $checkpoint->lng ? round(floatval($checkpoint->lng), static::$precision) : 0,
                ],

                'label'     => $checkpoint->lat . ',' . $checkpoint->lng,

                'id' => $checkpoint->getKey(),

				/**
				 * Optionally you can provide custom icons for the map markers,
				 * either as scalable SVG's, or PNG, which doesn't support scaling.
				 * If you don't provide icons, the map will use the standard Google marker pin.
				 */
                'icon' => [
                    'url'       => url('images/marker.png'),
                ],
            ];
        }

        return $data;
    }
}