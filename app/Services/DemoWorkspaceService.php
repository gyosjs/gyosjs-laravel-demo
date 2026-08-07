<?php

namespace App\Services;

use App\Models\DemoWorkspace;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;

class DemoWorkspaceService
{
    private const SESSION_KEY = 'demo_workspace_id';

    public function resolve(Session $session): DemoWorkspace
    {
        $workspace = $session->has(self::SESSION_KEY)
            ? DemoWorkspace::find($session->get(self::SESSION_KEY))
            : null;

        if (! $workspace) {
            $workspace = DB::transaction(function () {
                $workspace = DemoWorkspace::create(['last_seen_at' => now()]);
                $this->seed($workspace);

                return $workspace;
            });
            $session->put(self::SESSION_KEY, $workspace->getKey());
        } else {
            $workspace->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        return $workspace;
    }

    public function reset(DemoWorkspace $workspace): void
    {
        DB::transaction(function () use ($workspace) {
            $workspace->products()->delete();
            $this->seed($workspace);
            $workspace->forceFill(['last_seen_at' => now()])->save();
        });
    }

    public function seed(DemoWorkspace $workspace): void
    {
        $families = [
            ['Arc', 'Headphones', 'Audio', 'coral'],
            ['Field', 'Speaker', 'Audio', 'amber'],
            ['Grid', 'Keyboard', 'Desk', 'slate'],
            ['Draft', 'Notebook Stand', 'Desk', 'moss'],
            ['Beacon', 'Task Lamp', 'Lighting', 'sun'],
            ['Orbit', 'Desk Light', 'Lighting', 'sky'],
            ['Transit', 'Cable Kit', 'Travel', 'violet'],
            ['Roam', 'Power Pack', 'Travel', 'clay'],
            ['Frame', 'Monitor Arm', 'Desk', 'steel'],
        ];
        $editions = ['One', 'Two', 'Studio', 'Mini'];
        $rows = [];
        $index = 1;

        foreach ($families as [$prefix, $product, $category, $tone]) {
            foreach ($editions as $edition) {
                $rows[] = [
                    'sku' => sprintf('GY-%s-%03d', strtoupper(substr($prefix, 0, 2)), $index),
                    'name' => "$prefix $product $edition",
                    'category' => $category,
                    'status' => match ($index % 7) {
                        0 => 'archived',
                        3 => 'draft',
                        default => 'active',
                    },
                    'price' => 39 + (($index * 17) % 260) + .90,
                    'stock' => ($index * 13) % 91,
                    'description' => "A focused $category tool for compact workspaces. The $edition edition balances durable materials with a clear, repairable design.",
                    'image_tone' => $tone,
                    'created_at' => now()->subMinutes($index * 19),
                    'updated_at' => now()->subMinutes($index * 7),
                ];
                $index++;
            }
        }

        $workspace->products()->createMany($rows);
    }
}
