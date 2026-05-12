<?php

namespace App\Services;

use Phpml\Clustering\KMeans;

class WorkloadClusteringService
{
    public function analyze($dataset, $k = 3)
    {
        if (empty($dataset) || count($dataset) < $k) {
            return [];
        }

        // 1. Normalisasi Min-Max
        $normalizedData = $this->minMaxNormalization($dataset);

        // 2. Jalankan K-Means
        $kmeans = new KMeans($k);
        $clusters = $kmeans->cluster($normalizedData);

        // 3. Tentukan Label (Mana yang Tinggi, Ideal, Rendah)
        $clusterLabels = $this->determineClusterLabels($clusters);

        // 4. Petakan kembali SETIAP data point ke Labelnya
        $results = [];
        foreach ($dataset as $index => $point) {
            $normPoint = $normalizedData[$index];
            
            // Cari point ini ada di cluster mana
            foreach ($clusters as $clusterIdx => $clusterPoints) {
                if (in_array($normPoint, $clusterPoints)) {
                    $results[$index] = $clusterLabels[$clusterIdx];
                    break;
                }
            }
            
            // Fallback jika tidak ditemukan (seharusnya tidak terjadi)
            if (!isset($results[$index])) {
                $results[$index] = 'Beban Ideal';
            }
        }

        return $results;
    }

    private function minMaxNormalization($dataset)
    {
        $columns = count($dataset[0]);
        $rows = count($dataset);
        $normalized = [];

        for ($j = 0; $j < $columns; $j++) {
            $colValues = array_column($dataset, $j);
            $min = min($colValues);
            $max = max($colValues);
            $range = $max - $min;
            
            for ($i = 0; $i < $rows; $i++) {
                $normalized[$i][$j] = $range == 0 ? 0 : (float)(($dataset[$i][$j] - $min) / $range);
            }
        }

        return $normalized;
    }

    private function determineClusterLabels($clusters)
    {
        $metrics = [];
        foreach ($clusters as $idx => $points) {
            $total = 0;
            foreach ($points as $p) {
                $total += array_sum($p);
            }
            $metrics[$idx] = count($points) > 0 ? $total / count($points) : 0;
        }

        asort($metrics);
        $keys = array_keys($metrics);

        $labels = [];
        if (isset($keys[0])) $labels[$keys[0]] = 'Beban Rendah';
        if (isset($keys[1])) $labels[$keys[1]] = 'Beban Ideal';
        if (isset($keys[2])) $labels[$keys[2]] = 'Beban Tinggi';

        // Jika hanya ada 2 cluster yang terbentuk
        if (count($keys) == 2) {
            $labels[$keys[0]] = 'Beban Rendah';
            $labels[$keys[1]] = 'Beban Tinggi';
        }

        return $labels;
    }
}
